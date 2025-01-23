<?php

namespace humhub\modules\custom_pages\modules\template\models;

use humhub\components\ActiveRecord;
use humhub\modules\content\models\Content;
use humhub\modules\custom_pages\lib\templates\TemplateEngineFactory;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\modules\template\elements\BaseTemplateElementContent;
use humhub\modules\custom_pages\modules\template\elements\ContainerDefinition;
use humhub\modules\custom_pages\modules\template\elements\ContainerElement;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for all templates.
 *
 * Every template has an unique template name, which is used by the template engine to identify the template.
 *
 * A template can be related to multiple TemplateElements, which define the content type and name of the template placeholders.
 *
 * The template can define a default content for each placeholder, by creating an OwnerContent with the
 * given placeholder name.
 *
 * If no default content is given, and a TemplateContentOwner does not define an own OwnerContent for the placeholder, the placeholder is
 * either rendered empty if not in editmode or as default(empty) block if the edit mode is activated.
 *
 *
 * There are different types of templates:
 *
 *  - Layout: Root template which is not combinable with other templates.
 *  - Container: Template which is combinable with other templates.
 *
 * @property $id int
 * @property $name string
 * @property $source string
 * @property $engine string
 * @property $description string
 * @property $type string
 * @property $allow_for_spaces boolean
 * @property $allow_inline_activation boolean
 *
 * @property-read TemplateElement[] $elements
 */
class Template extends ActiveRecord implements TemplateContentOwner
{
    public const TYPE_LAYOUT = 'layout';
    public const TYPE_SNIPPET_LAYOUT = 'snippet-layout';
    public const TYPE_NAVIGATION = 'navigation';
    public const TYPE_CONTAINER = 'container';

    /**
     * @var TemplateElement[] all template elements used for the rendering process.
     */
    private $_elements;

    /**
     * @inheritdoc
     */
    public function init()
    {
        // Set default engine
        $this->engine = "twig";
    }

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'custom_pages_template';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'source' => 'Source',
            'allow_for_spaces' => 'Allow this layout in spaces',
            'allow_inline_activation' => 'Allow inline edit activation in inline editor',
            'description' => 'Description',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'required', 'on' => ['edit']],
            ['description', 'safe'],
            [['allow_for_spaces', 'allow_inline_activation'], 'integer'],
            [['name'], 'unique'],
            [['name', 'type'], 'string', 'max' => 100],
            [['type'], 'validType'],
            [['source'], 'required', 'on' => ['source']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['edit'] = ['name', 'description', 'allow_for_spaces', 'allow_inline_activation'];
        $scenarios['source'] = ['source'];
        return $scenarios;
    }

    /**
     * Validates the template type against allowed types.
     * @param type $attribute
     * @param type $model
     */
    public function validType($attribute, $model)
    {
        $validTypes = [self::TYPE_CONTAINER, self::TYPE_LAYOUT, self::TYPE_NAVIGATION];
        if (!in_array($this->type, $validTypes)) {
            $this->addError($attribute, 'Invalid template type!');
        }
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (isset($changedAttributes['name'])) {
            // Update to new template name when it is used as Allowed Template in Container Element
            $definitions = ContainerDefinition::find()
                ->where(['LIKE', 'dyn_attributes', '"templates":['])
                ->andWhere(['LIKE', 'dyn_attributes', '"' . $changedAttributes['name'] . '"']);
            foreach ($definitions->each() as $definition) {
                /* @var ContainerDefinition $definition */
                $oldTemplates = $definition->templates;
                $oldTemplateIndex = array_search($changedAttributes['name'], $oldTemplates);
                if ($oldTemplateIndex !== false) {
                    $oldTemplates[$oldTemplateIndex] = $this->name;
                    $definition->templates = $oldTemplates;
                    $definition->save();
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        // We just allow the template deletion if there are template owner relations.
        if (!$this->isInUse()) {
            foreach ($this->getContents()->all() as $content) {
                $content->hardDelete();
            }
            foreach ($this->elements as $element) {
                $element->delete();
            }
            return true;
        }

        return false;
    }

    public function isInUse()
    {
        if ($this->isLayout()) {
            return TemplateInstance::findByTemplateId($this->id, Content::STATE_PUBLISHED)->count() > 0;
        } else {
            return TemplateElement::find()
                ->leftJoin(ContainerElement::tableName(), TemplateElement::tableName() . '.id = ' . ContainerElement::tableName() . '.element_id')
                ->leftJoin(ContainerDefinition::tableName(), ContainerElement::tableName() . '.definition_id = ' . ContainerDefinition::tableName() . '.id')
                ->where([TemplateElement::tableName() . '.content_type' => ContainerElement::class])
                ->andWhere(['REGEXP', ContainerDefinition::tableName() . '.dyn_attributes', '"templates":[^\\]]*' . $this->id . '[,\\]]'])
                ->exists();
        }
    }

    public function getLinkedRecordsQuery(): ActiveQuery
    {
        if ($this->isLayout()) {
            return $this->getContents();
        } else {
            return Template::find()
                ->leftJoin(OwnerContent::tableName(), Template::tableName() . '.id = ' . OwnerContent::tableName() . '.owner_id')
                ->leftJoin(ContainerElement::tableName(), ContainerElement::tableName() . '.id = ' . OwnerContent::tableName() . '.content_id')
                ->leftJoin(ContainerDefinition::tableName(), ContainerDefinition::tableName() . '.id = ' . ContainerElement::tableName() . '.definition_id')
                ->where([OwnerContent::tableName() . '.owner_model' => Template::class])
                ->andWhere([OwnerContent::tableName() . '.content_type' => ContainerElement::class])
                ->andWhere(['REGEXP', ContainerDefinition::tableName() . '.dyn_attributes', '"templates":[^\\]]*' . $this->id . '[,\\]]']);
        }
    }

    /**
     * Checks if this template is a root layout template.
     * @return bool
     */
    public function isLayout()
    {
        return $this->type === self::TYPE_LAYOUT || $this->type === self::TYPE_SNIPPET_LAYOUT;
    }

    /**
     * Returns the TemplateElement of this template for a given placeholder name.
     * @param string $name placeholder name.
     * @return TemplateElement
     */
    public function getElement($name)
    {
        return TemplateElement::findOne(['template_id' => $this->id, 'name' => $name]);
    }

    /**
     * Returns all TemplateElement definitions for this template.
     * @return ActiveQuery
     */
    public function getElements()
    {
        return $this->hasMany(TemplateElement::class, ['template_id' => 'id']);
    }

    /**
     * Returns all Contents linked with this Template.
     * @return ActiveQuery
     */
    public function getContents(): ActiveQuery
    {
        return Content::find()->leftJoin(
            TemplateInstance::tableName(),
            Content::tableName() . '.object_model = :object_model AND ' .
                Content::tableName() . '.object_id = ' . TemplateInstance::tableName() . '.page_id',
            ['object_model' => CustomPage::class],
        )
            ->where([TemplateInstance::tableName() . '.template_id' => $this->id]);
    }

    /**
     * Renders the template for the given $owner or with all default content if
     * no $owner was given.
     *
     * This is done by merging all default OwnerContent instances with the overwritten
     * OwnerContent instances defined by the TemplateContentOwner $owner.
     *
     * @param ActiveRecord $owner
     * @return string
     */
    public function render(ActiveRecord $owner = null, $editMode = false, $containerItem = null)
    {
        $elementContents = $this->getElementContents($owner);

        $content = [];
        foreach ($elementContents as $elementContent) {
            $content[$elementContent->element->name] = new OwnerContentVariable([
                'elementContent' => $elementContent,
                'options' => [
                    'editMode' => $editMode,
                    'element_title' => $elementContent->element->getTitle(),
                    'item' => $containerItem,
                ],
            ]);
        }

        $content['assets'] = PHP_VERSION_ID >= 80000 ? new AssetVariable() : new AssetVariablePhp74();

        $engine = TemplateEngineFactory::create($this->engine);
        $result = $engine->render($this->name, $content);
        return $result;
    }

    /**
     * Merges the default OwnerContent instances with the OwnerContent instances of the given $owner.
     * If there is no default OwnerContent and no OwnerContent for the given $owner this function will create an
     * empty dummy content for the given placeholder.
     *
     * If no $owner is given, this function will just return default OwnerContent of this template and empty OwnerContent instances.
     *
     * @param ActiveRecord $owner the template owner
     * @return array
     */
    public function getContentElements(ActiveRecord $owner = null)
    {
        $result = [];
        $this->_elements = $this->getElements()->all();

        if (count($this->_elements) == 0) {
            return $result;
        }

        if ($owner != null) {
            // Non default content defined by owner
            $result = OwnerContent::findByOwner($owner)->all();
        }

        $ownerElementNames = array_map(function ($contentInstance) {
            return $contentInstance->element_name;
        }, $result);

        foreach ($this->_elements as $element) {
            if (!in_array($element->name, $ownerElementNames)) {
                $result[] = $element->getDefaultContent(true);
            }
        }

        return $result;
    }

    /**
     * @return BaseTemplateElementContent[]
     */
    public function getElementContents(TemplateInstance $templateInstance): array
    {
        if (!is_array($this->_elements)) {
            $this->_elements = $this->getElements()->all();
        }

        $elementContents = [];
        if ($this->_elements === []) {
            return $elementContents;
        }

        if ($templateInstance !== null) {
            // Non default content defined by owner
            $elementContents = BaseTemplateElementContent::find()
                ->where(['template_instance_id' => $templateInstance->id])
                ->all();
        }

        $elementNames = array_map(function ($elementContent) {
            return $elementContent->element->name;
        }, $elementContents);

        foreach ($this->_elements as $element) {
            if (!in_array($element->name, $elementNames)) {
                $elementContents[] = $element->getDefaultContent(true);
            }
        }

        return $elementContents;
    }

    private function getElementTitle($element_name)
    {
        if (!is_array($this->_elements)) {
            $this->_elements = $this->getElements()->all();
        }

        foreach ($this->_elements as $element) {
            if ($element->name === $element_name) {
                return $element->getTitle();
            }
        }
    }

    /**
     * Returns all templates of a given type as ActiveQuery.
     *
     * @param string $type
     * @return ActiveQuery
     */
    public static function findByType($type)
    {
        return self::find()->where(['type' => $type]);
    }


    /**
     * Returns all templates of a given type as array.
     *
     * @param string $type
     * @return array
     */
    public static function findAllByType($type)
    {
        return self::findByType($type)->all();
    }

    /**
     * Prepares a template selection array for the given query condition.
     *
     * @param array $condition query condition
     * @param string $keyFieldName
     * @return array selection array with template id as keys and template name as values.
     */
    public static function getSelection(array $condition = [], string $keyFieldName = 'id')
    {
        return ArrayHelper::map(self::find()->where($condition)->all(), $keyFieldName, 'name');
    }

    public function getTemplateId()
    {
        return $this->id;
    }

    public static function getTypeTitle(string $type): string
    {
        return match ($type) {
            self::TYPE_CONTAINER => Yii::t('CustomPagesModule.base', 'Container'),
            self::TYPE_SNIPPET_LAYOUT => Yii::t('CustomPagesModule.base', 'Snippet Layout'),
            self::TYPE_NAVIGATION => Yii::t('CustomPagesModule.base', 'Navigation'),
            default => Yii::t('CustomPagesModule.base', 'Layout'),
        };
    }

}
