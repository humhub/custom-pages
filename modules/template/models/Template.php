<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\models;

use humhub\components\ActiveRecord;
use humhub\modules\admin\permissions\ManageModules;
use humhub\modules\content\models\Content;
use humhub\modules\custom_pages\lib\templates\TemplateEngineFactory;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\modules\template\elements\BaseElementContent;
use humhub\modules\custom_pages\modules\template\elements\ContainerDefinition;
use humhub\modules\custom_pages\modules\template\elements\ContainerElement;
use humhub\modules\custom_pages\modules\template\services\TemplateInstanceRendererService;
use humhub\modules\custom_pages\modules\template\widgets\TemplateStructure;
use humhub\modules\custom_pages\permissions\ManagePages;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\web\View;

/**
 * This is the model class for all templates.
 *
 * Every template has an unique template name, which is used by the template engine to identify the template.
 *
 * A template can be related to multiple TemplateElements, which define the content type and name of the template placeholders.
 *
 * The template can define a default content for each placeholder, by creating an ElementContent with the
 * given placeholder name.
 *
 * If no default content is given, and a Template Instance does not define an own ElementContent for the placeholder,
 * the placeholder is either rendered empty if not in edit mode or as default(empty) block if the edit mode is activated.
 *
 *
 * There are different types of templates:
 *
 *  - Layout: Root template which is not combinable with other templates.
 *  - Container: Template which is combinable with other templates.
 *
 * @property int $id
 * @property string $name
 * @property string $source
 * @property string $css
 * @property string $js
 * @property string $engine
 * @property string $description
 * @property string $type
 * @property bool $allow_for_spaces
 * @property bool $is_default
 * @property string $created_at
 * @property int $created_by
 * @property string $updated_at
 * @property int $updated_by
 *
 * @property-read TemplateElement[] $elements
 */
class Template extends ActiveRecord
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
        $this->engine = 'twig';
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
            'name' => Yii::t('CustomPagesModule.template', 'Name'),
            'source' => Yii::t('CustomPagesModule.template', 'Source'),
            'css' => Yii::t('CustomPagesModule.template', 'Stylesheet'),
            'js' => Yii::t('CustomPagesModule.template', 'JavaScript'),
            'allow_for_spaces' => Yii::t('CustomPagesModule.template', 'Allow this layout in spaces'),
            'description' => Yii::t('CustomPagesModule.template', 'Description'),
            'type' => Yii::t('CustomPagesModule.template', 'Type'),
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
            [['allow_for_spaces'], 'boolean'],
            [['name'], 'unique'],
            [['name', 'type'], 'string', 'max' => 100],
            [['type'], 'in', 'range' => [self::TYPE_CONTAINER, self::TYPE_LAYOUT, self::TYPE_SNIPPET_LAYOUT, self::TYPE_NAVIGATION]],
            [['source'], 'required', 'on' => ['source']],
            [['css', 'js'], 'safe', 'on' => ['resources']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['edit'] = ['name', 'description', 'type', 'allow_for_spaces'];
        $scenarios['source'] = ['source'];
        $scenarios['resources'] = ['css', 'js'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function load($data, $formName = null)
    {
        return $this->canEdit() && parent::load($data, $formName);
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
                ->andWhere(['LIKE', 'dyn_attributes', '"templates":['])
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

        if ($this->is_default) {
            // Keep each default template as not updated in order to don't create a copy on next auto updating
            $this->updateAttributes([
                'updated_at' => null,
                'updated_by' => null,
            ]);
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

        if (!$this->canDelete()) {
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

    public function isInUse(): bool
    {
        if ($this->isLayout()) {
            return TemplateInstance::findByTemplateId($this->id, Content::STATE_PUBLISHED)->exists();
        } else {
            return ContainerDefinition::find()
                ->andWhere(['REGEXP', 'dyn_attributes', '"templates":\\[.*"' . addslashes(preg_quote($this->name, '/')) . '".*\\]'])
                ->exists();
        }
    }

    public function getLinkedRecordsQuery(): ActiveQuery
    {
        if ($this->isLayout()) {
            return $this->getContents();
        } else {
            return Template::find()
                ->innerJoin(TemplateElement::tableName(), Template::tableName() . '.id = ' . TemplateElement::tableName() . '.template_id')
                ->where([TemplateElement::tableName() . '.content_type' => ContainerElement::class])
                ->andWhere(['REGEXP', TemplateElement::tableName() . '.dyn_attributes', '"templates":\\[.*"' . preg_quote($this->name, '/') . '".*\\]']);
        }
    }

    /**
     * Checks if this template is a root layout template.
     * @return bool
     */
    public function isLayout(): bool
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
     * Register JS & CSS of the Template
     *
     * @return void
     */
    public function registerResources(): void
    {
        if ($this->css) {
            Yii::$app->view->registerCss($this->css);
        }

        if ($this->js) {
            Yii::$app->view->registerJs($this->js, View::POS_END);
        }
    }

    /**
     * Renders the template for the given $owner or with all default content if
     * no $owner was given.
     *
     * This is done by merging all default ElementContent instances with the overwritten
     * ElementContent instances defined by the Template Instance.
     *
     * @param TemplateInstance|null $templateInstance
     * @return string
     */
    public function render(?TemplateInstance $templateInstance = null)
    {
        $result = '';

        if (TemplateInstanceRendererService::inEditMode() && $templateInstance && $templateInstance->isPage()) {
            $result = TemplateStructure::widget(['templateInstance' => $templateInstance]);
        }

        $elementContents = $this->getElementContents($templateInstance);

        $content = [];
        foreach ($elementContents as $elementContent) {
            $content[$elementContent->element->name] = $elementContent->getTemplateVariable();
        }

        $content['assets'] = new AssetVariable();

        $engine = TemplateEngineFactory::create($this->engine);

        return $result . $engine->render($this->name, $content);
    }

    /**
     * @param TemplateInstance|null $templateInstance
     * @return BaseElementContent[]
     */
    public function getElementContents(?TemplateInstance $templateInstance = null): array
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
            $elementContents = BaseElementContent::find()
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

        usort($elementContents, fn($a, $b) => $a->element_id <=> $b->element_id);

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

    public static function getTypeOptions(): array
    {
        return [
            self::TYPE_LAYOUT => Yii::t('CustomPagesModule.base', 'Layout'),
            self::TYPE_SNIPPET_LAYOUT => Yii::t('CustomPagesModule.base', 'Snippet'),
            self::TYPE_CONTAINER => Yii::t('CustomPagesModule.base', 'Container'),
        ];
    }

    public static function getTypeTitle(string $type): string
    {
        return match ($type) {
            self::TYPE_CONTAINER => Yii::t('CustomPagesModule.base', 'Container'),
            self::TYPE_SNIPPET_LAYOUT => Yii::t('CustomPagesModule.base', 'Snippet'),
            self::TYPE_NAVIGATION => Yii::t('CustomPagesModule.base', 'Navigation'),
            default => Yii::t('CustomPagesModule.base', 'Layout'),
        };
    }

    /**
     * Checks if this template and its elements can be edited
     *
     * @return bool
     * @since 1.11
     */
    public function canEdit(): bool
    {
        if (!$this->isNewRecord && $this->is_default &&
            !Yii::$app->getModule('custom_pages')->allowUpdateDefaultTemplates) {
            return false;
        }

        return Yii::$app->user->can([ManageModules::class, ManagePages::class]);
    }

    /**
     * Checks if this template and its elements can be deleted
     *
     * @return bool
     * @since 1.11
     */
    public function canDelete(): bool
    {
        if (!$this->isNewRecord && $this->is_default) {
            return false;
        }

        return Yii::$app->user->can([ManageModules::class, ManagePages::class]);
    }

    public function saveCopy(): bool
    {
        $elements = $this->elements;
        unset($this->id);
        $this->is_default = 0;

        if (!$this->save()) {
            return false;
        }

        foreach ($elements as $element) {
            $defaultContent = $element->getDefaultContent();
            unset($element->id);
            $element->setOldAttributes(null);
            $element->template_id = $this->id;
            $element->scenario = TemplateElement::SCENARIO_EDIT_ADMIN;
            if ($element->save() && $defaultContent) {
                unset($defaultContent->id);
                $defaultContent->setOldAttributes(null);
                $defaultContent->scenario = BaseElementContent::SCENARIO_EDIT_ADMIN;
                $defaultContent->element_id = $element->id;
                $defaultContent->save();
            }
        }

        return true;
    }
}
