<?php

namespace humhub\modules\custom_pages\modules\template\models;

use humhub\modules\custom_pages\lib\templates\TemplateEngineFactory;
use humhub\components\ActiveRecord;
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
 * @var $id int
 * @var $name string
 * @var $source string
 * @var $allow_for_spaces boolean
 * @var $allow_inline_activation boolean
 */
class Template extends ActiveRecord implements TemplateContentOwner
{

    const TYPE_LAYOUT = 'layout';
    const TYPE_SNIPPED_LAYOUT = 'snipped-layout';
    const TYPE_NAVIGATION = 'navigation';
    const TYPE_CONTAINER = 'container';
    
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
            [['allow_for_spaces', 'isLyout', 'allow_inline_activation'], 'integer'],
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
    public function validType($attribute, $model) {
        $validTypes = [self::TYPE_CONTAINER, self::TYPE_LAYOUT, self::TYPE_NAVIGATION];
        if(!in_array($this->type, $validTypes)) {
            $this->addError($attribute, 'Invalid template type!');
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if(!parent::beforeDelete()) {
            return false;
        }
        
        // We just allow the template deletion if there are template owner relations.
        if(!$this->isInUse()) {
            foreach ($this->elements as $element) {
                $element->delete();
            }
            return true;
        }
        
        return false;
    }
    
    public function isInUse()
    {
        if($this->isLayout()) {
            return TemplateInstance::findByTemplateId($this->id)->count() > 0;
        } else {
            return ContainerContentTemplate::find()->where(['template_id' => $this->id])->count() > 0;
        }
    }
    
    /**
     * Checks if this template is a root layout template.
     * @return boolean
     */
    public function isLayout()
    {
        return $this->type === self::TYPE_LAYOUT || $this->type === self::TYPE_SNIPPED_LAYOUT;
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
        return $this->hasMany(TemplateElement::className(), ['template_id' => 'id']);
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
        $contentElements = $this->getContentElements($owner);
        
        if($owner == null) {
            $owner = $this;
        }

        $content = [];
        foreach ($contentElements as $contentElement) {
            $options = [
                'editMode' => $editMode,
                'element_title' => $this->getElementTitle($contentElement->element_name),
                'owner_model' => $owner->className(),
                'owner_id' => $owner->id,
                'item' => $containerItem
            ];
            
            $content[$contentElement->element_name] = new OwnerContentVariable(['ownerContent' => $contentElement, 'options' => $options]);
        }
        
        $content['assets'] = new AssetVariable();
        
        if($containerItem) {
            //$content['item'] = new ContainerItemVariable(['item' => $containerItem]);
        }

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

        $ownerElementNames = array_map(function($contentInstance) {
            return $contentInstance->element_name;
        }, $result);

        foreach ($this->_elements as $element) {
            if (!in_array($element->name, $ownerElementNames)) {
                $result[] = $element->getDefaultContent(true);
            }
        }

        return $result;
    }
    
    private function getElementTitle($element_name)
    {
        if(!$this->_elements) {
            $this->_elements = $this->getElements()->all();
        }
        
        foreach ($this->_elements as $element) {
            if($element->name === $element_name) {
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
     * @return array selection array with template id as keys and template name as values.
     */
    public static function getSelection($condition = [])
    {
        return ArrayHelper::map(self::find()->where($condition)->all(), 'id', 'name');
    }

    public function getTemplateId()
    {
        return $this->id;
    }

}
