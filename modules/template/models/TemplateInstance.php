<?php

namespace humhub\modules\custom_pages\modules\template\models;

use humhub\components\ActiveRecord;

/**
 * This is the model class for table "custom_pages_template".
 */
class TemplateInstance extends ActiveRecord implements TemplateContentOwner
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => \humhub\components\behaviors\PolymorphicRelation::className(),
                'mustBeInstanceOf' => [ActiveRecord::className()]
            ]
        ];
    }
    
    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'custom_pages_template_container';
    }

    public function rules()
    {
        return [
            [['template_id', 'object_model', 'object_id'], 'required'],
            [['template_id'], 'integer'],
        ];
    }
    
    public function afterDelete()
    {
        forEach(OwnerContent::findByOwner($this)->all() as $content) {
            $content->delete();
        }
    }
    
    public static function getDefaultElement($id, $elementName)
    {
        $pageTemplate = ($id instanceof TemplateInstance) ? $id 
                : self::find()->where(['custom_pages_page_template.id' => $id])->joinWith('template')->one();
        return $pageTemplate->template->getElement($elementName);
    }
    
    public static function findByTemplateId($templateId) {
        return self::find()->where(['template_id' => $templateId]);
    }
    
    public function render($editMode = false)
    {
        return $this->template->render($this, $editMode);
    }

    public function getTemplate()
    {
        return $this->hasOne(Template::className(), ['id' => 'template_id']);
    }

    public function getTemplateId()
    {
        return $this->template_id;
    }
    
    public static function deleteByOwner(\yii\db\ActiveRecord $owner)
    {
        $container = self::findOne(['object_model' => $owner->className(), 'object_id' => $owner->getPrimaryKey()]);
        if($container != null) {
            $container->delete();
        }
    }

}
