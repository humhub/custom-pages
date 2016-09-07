<?php

namespace humhub\modules\custom_pages\modules\template\models;

use Yii;

/**
 * This is the model class for table "custom_pages_page".
 *
 * The followings are the available columns in table 'custom_pages_page':
 */
class ContainerContentDefinition extends ContentDefinition
{

    public $allowedTemplateSelection = [];

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'custom_pages_template_container_content_definition';
    }

    public function rules()
    {
        return [
            [['allowedTemplateSelection'], 'safe'],
            [['allow_multiple', 'is_inline'], 'integer']
        ];
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'allow_multiple' => Yii::t('CustomPagesModule.modules_template_models_CotnainerContent', 'Allow multiple items?'),
            'allowedTemplateSelection' => Yii::t('CustomPagesModule.modules_template_models_CotnainerContent', 'Allowed Templates'),
            'is_inline' => Yii::t('CustomPagesModule.modules_template_models_CotnainerContent','Render items as inline-blocks within the inline editor?')
        ]);
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->saveAllowedTemplateSelection();

        parent::afterSave($insert, $changedAttributes);
    }
    
    public function saveAllowedTemplateSelection()
    {
        if (!empty($this->allowedTemplateSelection)) {
            ContainerContentTemplate::deleteAll(['definition_id' => $this->id]);
            foreach ($this->allowedTemplateSelection as $allowedTemplateId) {
                $allowedTemplate = new ContainerContentTemplate();
                $allowedTemplate->template_id = $allowedTemplateId;
                $allowedTemplate->definition_id = $this->id;
                $allowedTemplate->save();
            }
        }
    }

    public function beforeDelete()
    {
        ContainerContentTemplate::deleteAll(['definition_id' => $this->id]);
        return parent::beforeDelete();
    }
    
    public function getAllowedTemplates()
    {
        if (empty($this->templates)) {
            return Template::findAllByType(Template::TYPE_CONTAINER);
        }
        return $this->templates;
    }

    public function isSingleAllowedTemplate()
    {
        return count($this->templates) === 1;
    }

    public function initAllowedTemplateSelection($actualSelection = true)
    {
        $this->allowedTemplateSelection = $this->getAllowedTemplateArray($actualSelection);
    }

    protected function getAllowedTemplateArray($actualSelection = true)
    {
        $selectionTemplates = ($actualSelection) ? $this->templates : $this->allowedTemplates;
        
        $result = [];
        foreach ($selectionTemplates as $allowedTemplate) {
            $result[] = $allowedTemplate->id;
        }

        return $result;
    }

    public function getContentTemplates()
    {
        return $this->hasMany(ContainerContentTemplate::className(), ['definition_id' => 'id']);
    }

    public function getTemplates()
    {
        return $this->hasMany(Template::className(), ['id' => 'template_id'])
                        ->via('contentTemplates')->all();
    }
}
