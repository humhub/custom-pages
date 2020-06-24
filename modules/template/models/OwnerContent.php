<?php

namespace humhub\modules\custom_pages\modules\template\models;

use Yii;
use humhub\components\ActiveRecord;

/**
 * This is the model class for table "custom_pages_template_content".
 *
 * An OwnerContent instance is used to assign a content to a template placeholder.
 * The owner of the content can either be a Template (default content) or an TemplateContentOwner (e.g. TemplateInstance, ContainerContentItem).
 */
class OwnerContent extends ActiveRecord
{
    private $ownerInstance;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => \humhub\components\behaviors\PolymorphicRelation::class,
                'mustBeInstanceOf' => [TemplateContentActiveRecord::class],
                'classAttribute' => 'content_type',
                'pkAttribute' => 'content_id'
            ]
        ];
    }

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'custom_pages_template_owner_content';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['owner_model', 'owner_id', 'content_type', 'content_id', 'element_name'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if ($this->getInstance() != null) {
            $this->getInstance()->delete();
        }

        return parent::beforeDelete();
    }

    /**
     * Returns the underlying TemplateContentActiveRecord instance.
     *
     * If $createDummy is set to true, this function will return a empty dummy
     * object of this content_type.
     *
     * @param type $createDummy
     * @return TemplateContentActiveRecord
     */
    public function getInstance($createDummy = false)
    {

        if ($this->getPolymorphicRelation() == null && $createDummy) {
            return Yii::createObject($this->content_type);
        }

        return $this->getPolymorphicRelation();
    }

    /**
     * Returns the label of this content_type.
     * @return type
     */
    public function getLabel()
    {
        return $this->getInstance(true)->getLabel();
    }

    /**
     * Sets the owner model of this content.
     *
     * This function either accepts an TemplateContentOwner instance or an owner_model className
     * and id.
     *
     * @param TemplateContentOwner $owner
     * @param type $id
     */
    public function setOwner($owner, $id = null)
    {
        $this->owner_model = is_string($owner) ? $owner : get_class($owner);
        $this->owner_id = ($id == null) ? $owner->id : $id;
        $this->ownerInstance = null;
    }

    /**
     * Returns the content owner.
     *
     * @return TemplateContentOwner
     */
    public function getOwner()
    {
        if($this->ownerInstance == null) {
            $this->ownerInstance = self::getOwnerModel($this->owner_model, $this->owner_id);
        }

        return $this->ownerInstance;
    }

    public static function getOwnerModel($model, $id)
    {
        return call_user_func($model."::findOne", ['id' => $id]);
    }

    /**
     * Sets the object_model and object_id by means of the given $content instance.
     *
     * @param TemplateContentActiveRecord $content
     */
    public function setContent(TemplateContentActiveRecord $content)
    {
        $this->content_type = get_class($content);
        $this->content_id = $content->id;
    }

    public function copy()
    {
        $copy = new OwnerContent();
        $copy->use_default = $this->use_default;
        $copy->element_name = $this->element_name;
        $copy->content_type = $this->content_type;
        return $copy;
    }

    /**
     * Returns a copy of the related content instance.
     *
     * @return \humhub\modules\custom_pages\modules\template\models\TemplateContentActiveRecord
     */
    public function copyContent()
    {
        return $this->getPolymorphicRelation()->copy();
    }

    /**
     * Renders the related content instance.
     *
     * If $contentOnly is set to false the content is rendered with it's container.
     * The container's attributes can be set by means of the $options array.
     *
     * @param boolean $contentOnly
     * @param array $optoins
     * @return string
     */
    public function render($options = [])
    {
        if($this->use_default) {
            return ($this->isDefault())
                    ? $this->renderEmpty($options)
                    : $this->defaultContent->render($options);
        }

        $instance = $this->getInstance();
        if($instance != null) {
            return $instance->render($options);
        }
    }

    public function renderEmpty($options = [])
    {
        return Yii::createObject($this->content_type)->renderEmpty($options);
    }

    public function isEmpty()
    {
        return $this->getInstance() == null;
    }

    public function isDefault()
    {
        return $this->owner_model === Template::class;
    }

    public function getDefaultContent()
    {
        return self::findByOwner(Template::class, $this->owner->getTemplateId(), $this->element_name)->one();
    }

    /**
     * Find all OwnerContent instances of the given owner.
     *
     * @param type $ownerClass
     * @param type $ownerId
     * @param type $elementName
     * @return \yii\db\ActiveQuery
     */
    public static function findByOwner($ownerClass, $ownerId = null, $elementName = null)
    {
        if ($ownerClass instanceof \yii\db\ActiveRecord) {
            $elementName = $ownerId;
            $ownerId = $ownerClass->getPrimaryKey();
            $ownerClass = get_class($ownerClass);
        }

        $query = self::find()->where(['owner_model' => $ownerClass, 'owner_id' => $ownerId]);

        if ($elementName != null) {
            $query->andWhere(['element_name' => $elementName]);
        }

        return $query;
    }

    /**
     * Deletes all OwnerContent instances of the given owner.
     *
     * @param type $ownerClass
     * @param type $ownerId
     * @param type $elementName
     */
    public static function deleteByOwner($ownerClass, $ownerId = null, $elementName = null)
    {
        // We can't use delteAll since it won't trigger the afetDelete
        foreach (self::findByOwner($ownerClass, $ownerId, $elementName)->all() as $instance) {
            $instance->delete();
        }
    }

     public static function findByContent($contentType, $contentId = null)
    {
        if ($contentType instanceof \yii\db\ActiveRecord) {
            $contentId = $contentType->getPrimaryKey();
            $contentType = get_class($contentType);
        }

        return self::findOne(['content_type' => $contentType, 'content_id' => $contentId]);
    }


}
