<?php

namespace humhub\modules\custom_pages\modules\template\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "custom_pages_template".
 *
 * TemplateElements represent the placeholders of a template.
 * A TemplateElement consists of an name which is unique within the template and content type definition.
 *
 * @property $name string
 * @property $content_type string
 * @property $template_id int
 * @property $title string
 */
class TemplateElement extends ActiveRecord
{
    public const SCENARIO_CREATE = 'create';
    public const SCENARIO_EDIT = 'edit';
    public const SCENARIO_EDIT_ADMIN = 'edit-admin';

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'custom_pages_template_element';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'content_type', 'template_id'], 'required'],
            [['name', 'title', 'content_type'], 'string', 'length' => [2, 100]],
            ['name', 'match', 'pattern' => '/^[a-zA-Z][a-zA-Z0-9_]+$/', 'message' => Yii::t('CustomPagesModule.model', 'The element name must contain at least two characters without spaces or special signs except \'_\'')],
            ['name', 'uniqueTemplateElementName', 'on' => ['create']],
            [['template_id'], 'integer'],
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => ['name', 'content_type', 'template_id', 'title'],
            self::SCENARIO_EDIT_ADMIN => ['title'],
            self::SCENARIO_EDIT => [],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('CustomPagesModule.model', 'Placeholder name'),
            'title' => Yii::t('CustomPagesModule.model', 'Label'),
        ];
    }

    public function getTitle()
    {
        return ($this->title) ? $this->title : $this->name;
    }

    /**
     * This validator gets sure each element name is used only once for a template.
     *
     * @param type $attribute
     * @param type $params
     * @return type
     */
    public function uniqueTemplateElementName($attribute, $params)
    {
        $templateElementCount = self::find()->where(['template_id' => $this->template_id, 'name' => $this->name])->count();
        if ($templateElementCount > 0) {
            $this->addError($attribute, Yii::t('CustomPagesModule.model', 'The given element name is already in use for this template.'));
        }
    }

    /**
     * This function will create a new OwnerContent related to $owner for this placeholder.
     * This will overwrite the default content of a template placeholder for the given
     * $owner instance.
     *
     * $content is the actual content instance of type TemplateContentActiveRecord which will
     * be assigned to this placeholder for the given $owner.
     *
     * If the given $content instance was not persisted yet, it will be saved first.
     *
     * If the $owner is of type Template it will be saved as default content of the elements template.
     *
     * Note that all current OwnerContent entries for this placeholder owned by $owner are delted.
     *
     * @param ActiveRecord $owner the owner
     * @param TemplateContentActiveRecord $content
     * @return OwnerContent the new created owner content instance.
     */
    public function saveInstance(ActiveRecord $owner, TemplateContentActiveRecord $content, $useDefault = false)
    {
        $content->save();

        if ($owner instanceof Template) {
            return $this->saveAsDefaultContent($content);
        }

        // Delete all current default content.
        OwnerContent::deleteByOwner($owner, $this->name);

        $ownerContent = new OwnerContent();
        $ownerContent->use_default = $useDefault;
        $ownerContent->setOwner($owner);
        $ownerContent->setContent($content);
        $ownerContent->element_name = $this->name;
        $ownerContent->save();
        return $ownerContent;
    }

    /**
     * Sets the gien $content as default content for this placeholder.
     *
     * Note that the current default content of this placeholder will be delted.
     *
     * @param TemplateContentActiveRecord $content
     * @return bool
     */
    public function saveAsDefaultContent(TemplateContentActiveRecord $content)
    {
        if (get_class($content) != $this->content_type) {
            return false;
        }

        // Delete all current default content elements.
        OwnerContent::deleteByOwner(Template::class, $this->template_id, $this->name);

        if ($content->save()) {
            $contentInstance = new OwnerContent();
            $contentInstance->element_name = $this->name;
            $contentInstance->setOwner(Template::class, $this->template_id);
            $contentInstance->setContent($content);
            return $contentInstance->save();
        } else {
            return false;
        }
    }

    /**
     * Returns the default OwnerContent instance for this placeholder.
     *
     * If no default content was found and $createDummy is set to true, this
     * function will return an empty dummy OwnerContent instance.
     *
     * @param bool $createDummy
     * @return OwnerContent
     */
    public function getDefaultContent($createDummy = false)
    {
        $content = OwnerContent::findByOwner(Template::class, $this->template_id, $this->name)->one();
        if ($content == null && $createDummy) {
            $content = new OwnerContent();
            $content->setOwner(Template::class, $this->template_id);
            $content->element_name = $this->name;
            $content->content_type = $this->content_type;
        }
        return $content;
    }

    /**
     * Checks if there is a default content for this placeholder
     * @return type
     */
    public function hasDefaultContent()
    {
        return OwnerContent::findByOwner(Template::class, $this->template_id, $this->name)->count() > 0;
    }

    /**
     * Returns the OwnerContent of this placeholder owned by the given $owner or
     * null if no OwnerContent was found.
     *
     * @param ActiveRecord $owner
     * @return OwnerContent
     */
    public function getOwnerContent(ActiveRecord $owner)
    {
        return OwnerContent::findByOwner(get_class($owner), $owner->getPrimaryKey(), $this->name)->one();
    }

    /**
     * @inhertidoc
     */
    public function afterDelete()
    {
        OwnerContent::deleteByOwner(Template::class, $this->template_id, $this->name);
        $templateOwners = TemplateInstance::findByTemplateId($this->template_id)->all();

        foreach ($templateOwners as $owner) {
            OwnerContent::deleteByOwner($owner, $this->name);
        }

        parent::afterDelete();
    }

    public function getTemplateContent(): TemplateContentActiveRecord
    {
        return Yii::createObject($this->content_type);
    }

    /**
     * Returns the label of the related content type.
     * @return string
     */
    public function getLabel()
    {
        return $this->getTemplateContent()->getLabel();
    }



}
