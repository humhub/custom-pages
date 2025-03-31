<?php

namespace humhub\modules\custom_pages\modules\template\models;

use humhub\components\ActiveRecord;
use humhub\components\behaviors\PolymorphicRelation;
use humhub\modules\content\models\Content;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\modules\template\elements\BaseElementContent;
use humhub\modules\custom_pages\modules\template\elements\ContainerItem;
use yii\db\ActiveQuery;

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
 * @property-read ContainerItem|null $containerItem
 */
class TemplateInstance extends ActiveRecord
{
    public const TYPE_PAGE = 'page';
    public const TYPE_CONTAINER = 'container';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => PolymorphicRelation::class,
                'mustBeInstanceOf' => [ActiveRecord::class],
            ],
        ];
    }

    /**
     * @inheritdoc
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
    public function beforeDelete()
    {
        foreach (BaseElementContent::findAll(['template_instance_id' => $this->id]) as $content) {
            $content->delete();
        }

        return parent::beforeDelete();
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

    public function render(string $mode = '')
    {
        return $this->template->render($this, $mode);
    }

    public function getTemplate()
    {
        return $this->hasOne(Template::class, ['id' => 'template_id']);
    }

    public function getPage(): ActiveQuery
    {
        return $this->hasOne(CustomPage::class, ['id' => 'page_id']);
    }

    public function getContainerItem(): ActiveQuery
    {
        return $this->hasOne(ContainerItem::class, ['id' => 'container_item_id']);
    }

    public static function findByOwner(ActiveRecord $owner): ?self
    {
        if ($owner instanceof CustomPage) {
            return self::findOne(['page_id' => $owner->id, 'container_item_id' => null]);
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

    /**
     * Get root template instance of the Container Item,
     * Return this if the instance is already a root of the Custom Page
     *
     * @return self|null
     */
    public function getRoot(): ?self
    {
        if ($this->container_item_id === null) {
            return $this;
        }

        return self::findOne(['page_id' => $this->page_id, 'container_item_id' => null]);
    }

    public function getType(): string
    {
        return $this->container_item_id === null ? self::TYPE_PAGE : self::TYPE_CONTAINER;
    }

    public function isPage(): bool
    {
        return $this->getType() === self::TYPE_PAGE;
    }

    public function isContainer(): bool
    {
        return $this->getType() === self::TYPE_CONTAINER;
    }
}
