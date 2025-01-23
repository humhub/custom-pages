<?php

namespace humhub\modules\custom_pages\modules\template\models;

use humhub\components\ActiveRecord;
use humhub\modules\content\models\Content;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\modules\template\elements\ContainerItem;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * A TemplateInstance represents an actual instantiation of a Template model.
 * The TemplateInstance can be for example a Page or Snippet related by the PolymorphicRelation behaviour.
 *
 * @property int $id
 * @property int $page_id
 * @property int $template_id
 * @property int|null $container_item_id
 *
 * @property-read Template $template
 * @property-read CustomPage $page
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
                'mustBeInstanceOf' => [ActiveRecord::class],
            ],
        ];
    }

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'custom_pages_template_instance';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['template_id', 'page_id'], 'required'],
            [['template_id', 'page_id'], 'integer'],
            [['container_item_id'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        foreach (OwnerContent::findByOwner($this)->all() as $content) {
            $content->delete();
        }
    }

    /**
     * Returns the default element of the element identified by $elementName of the given TemplateInstance identified by $id.
     *
     * @param \humhub\modules\custom_pages\modules\template\models\TemplateInstance|int $id
     * @param string $elementName
     * @return type
     */
    public static function getDefaultElement($id, $elementName)
    {
        $pageTemplate = ($id instanceof TemplateInstance) ? $id : self::find()->where(['custom_pages_page_template.id' => $id])->joinWith('template')->one();
        return $pageTemplate->template->getElement($elementName);
    }

    public static function findByTemplateId($templateId, ?int $contentState = null): ActiveQuery
    {
        $query = self::find()->where(['template_id' => $templateId]);

        if ($contentState !== null) {
            $query->leftJoin(
                Content::tableName(),
                Content::tableName() . '.object_model = :object_model AND ' .
                Content::tableName() . '.object_id = ' . self::tableName() . '.page_id',
                ['object_model' => CustomPage::class],
            )
                ->andWhere([Content::tableName() . '.state' => $contentState]);
        }

        return $query;
    }

    public function render($editMode = false)
    {
        return $this->template->render($this, $editMode);
    }

    public function getTemplate()
    {
        return $this->hasOne(Template::class, ['id' => 'template_id']);
    }

    public function getPage(): ActiveQuery
    {
        return $this->hasOne(Template::class, ['id' => 'page_id']);
    }

    public function getTemplateId()
    {
        return $this->template_id;
    }

    public static function findByOwner(ActiveRecord $owner): ?self
    {
        if ($owner instanceof CustomPage) {
            return self::findOne(['page_id' => $owner->id, ['IS', 'container_item_id', new Expression('NULL')]]);
        }

        if ($owner instanceof ContainerItem) {
            return self::findOne(['container_item_id' => $owner->id]);
        }

        return null;
    }

    public static function deleteByOwner(ActiveRecord $owner)
    {
        $templateInstance = self::findByOwner($owner);
        return $templateInstance ? $templateInstance->delete() : false;
    }

    public function getCacheKey(): string
    {
        return get_class($this) . $this->getPrimaryKey();
    }
}
