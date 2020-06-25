<?php

namespace humhub\modules\custom_pages\modules\template\models;

use humhub\components\ActiveRecord;

/**
 * A TemplateInstance represents an acutal instantiation of an Template model.
 * The TemplateInstance can be for example a Page or Snippet related by the PolymorphicRelation behaviour.
 *
 * @property int $id
 * @property string object_model
 * @property int object_id
 * @property int template_id
 *
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
                'class' => \humhub\components\behaviors\PolymorphicRelation::class,
                'mustBeInstanceOf' => [ActiveRecord::class]
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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['template_id', 'object_model', 'object_id'], 'required'],
            [['template_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        forEach (OwnerContent::findByOwner($this)->all() as $content) {
            $content->delete();
        }
    }

    /**
     * Returns the default element of the element identified by $elementName of the given TemplateInstance identified by $id.
     *
     * @param \humhub\modules\custom_pages\modules\template\models\TemplateInstance|integer $id
     * @param string $elementName
     * @return type
     */
    public static function getDefaultElement($id, $elementName)
    {
        $pageTemplate = ($id instanceof TemplateInstance) ? $id : self::find()->where(['custom_pages_page_template.id' => $id])->joinWith('template')->one();
        return $pageTemplate->template->getElement($elementName);
    }

    public static function findByTemplateId($templateId)
    {
        return self::find()->where(['template_id' => $templateId]);
    }

    public function render($editMode = false)
    {
        return $this->template->render($this, $editMode);
    }

    public function getTemplate()
    {
        return $this->hasOne(Template::class, ['id' => 'template_id']);
    }

    public function getTemplateId()
    {
        return $this->template_id;
    }

    public static function findByOwner(ActiveRecord $owner)
    {
        return self::findOne(['object_model' => get_class($owner), 'object_id' => $owner->getPrimaryKey()]);
    }

    public static function deleteByOwner(ActiveRecord $owner)
    {
        $container = self::findOne(['object_model' => get_class($owner), 'object_id' => $owner->getPrimaryKey()]);
        if ($container) {
            return $container->delete();
        }
        return false;
    }

}
