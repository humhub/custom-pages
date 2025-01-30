<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\models;

use humhub\modules\custom_pages\modules\template\elements\BaseTemplateElementContent;
use humhub\modules\custom_pages\modules\template\elements\ContainerItem;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "custom_pages_template".
 *
 * TemplateElements represent the placeholders of a template.
 * A TemplateElement consists of a name which is unique within the template and content type definition.
 *
 * @property int $id
 * @property int $template_id
 * @property string $name
 * @property string $content_type
 * @property string $title
 * @property string $dyn_attributes
 *
 * @property-read BaseTemplateElementContent[] $contents
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
     * This function will create a new ElementContent related to $owner for this placeholder.
     * This will overwrite the default content of a template placeholder for the given
     * $owner instance.
     *
     * $content is the actual content instance of type BaseTemplateElementContent which will
     * be assigned to this placeholder for the given $owner.
     *
     * If the given $content instance was not persisted yet, it will be saved first.
     *
     * If the $owner is of type Template it will be saved as default content of the elements template.
     *
     * Note that all current ElementContent entries for this placeholder owned by $owner are delted.
     *
     * @param ActiveRecord $owner the owner
     * @param BaseTemplateElementContent $content
     * @return BaseTemplateElementContent|null the new created element content instance.
     */
    public function saveInstance(ActiveRecord $owner, BaseTemplateElementContent $content): ?BaseTemplateElementContent
    {
        if ($owner instanceof Template) {
            return $this->saveAsDefaultContent($content);
        }

        $content->element_id = $this->id;
        if ($owner instanceof TemplateInstance) {
            $content->template_instance_id = $owner->id;
        } elseif ($owner instanceof ContainerItem) {
            $content->template_instance_id = $owner->templateInstance?->id;
        }

        if ($content->element_id && $content->template_instance_id) {
            // Delete old content of the same element and template instance
            $oldContent = BaseTemplateElementContent::findOne([
                'element_id' => $content->element_id,
                'template_instance_id' => $content->template_instance_id,
            ]);
            $oldContent && $oldContent->delete();
        }

        return $content->save() ? $content : null;
    }

    /**
     * Sets the gien $content as default content for this placeholder.
     *
     * Note that the current default content of this placeholder will be delted.
     *
     * @param BaseTemplateElementContent $content
     * @return BaseTemplateElementContent|null
     */
    public function saveAsDefaultContent($content): ?BaseTemplateElementContent
    {
        if (get_class($content) != $this->content_type) {
            return null;
        }

        $content->element_id = $this->id;

        if ($content->element_id) {
            // Delete old default content of the element
            $oldDefaultContent = BaseTemplateElementContent::findOne([
                'element_id' => $content->element_id,
                'template_instance_id' => null,
            ]);
            $oldDefaultContent && $oldDefaultContent->delete();
        }

        return $content->save() ? $content : null;
    }

    /**
     * Returns the default ElementContent instance for this placeholder.
     *
     * If no default content was found and $createDummy is set to true, this
     * function will return an empty dummy ElementContent instance.
     *
     * @param bool $createDummy
     * @return BaseTemplateElementContent|null
     */
    public function getDefaultContent(bool $createDummy = false): ?BaseTemplateElementContent
    {
        /* @var $content BaseTemplateElementContent */
        $content = BaseTemplateElementContent::find()
            ->where(['element_id' => $this->id])
            ->andWhere(['IS', 'template_instance_id', new Expression('NULL')])
            ->one();

        if ($content === null && $createDummy) {
            $content = Yii::createObject($this->content_type);
            $content->element_id = $this->id;
        }

        return $content;
    }

    /**
     * Checks if there is a default content for this placeholder
     *
     * @return bool
     */
    public function hasDefaultContent(): bool
    {
        return BaseTemplateElementContent::find()
            ->where(['element_id' => $this->id])
            ->andWhere(['IS', 'template_instance_id', new Expression('NULL')])
            ->exists();
    }

    /**
     * Returns all TemplateElement definitions for this template.
     * @return ActiveQuery
     */
    public function getContents(): ActiveQuery
    {
        return $this->hasMany(BaseTemplateElementContent::class, ['element_id' => 'id']);
    }

    /**
     * @inhertidoc
     */
    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        foreach ($this->contents as $elementContent) {
            $elementContent->delete();
        }

        return true;
    }

    public function getTemplateContent(): BaseTemplateElementContent
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
