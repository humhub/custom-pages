<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\content\components\ActiveQueryContent;
use Yii;
use yii\db\ActiveQuery;

/**
 * Abstract class to manage Element Content list of the ContentActiveRecords
 *
 * Dynamic attributes:
 * @property array $space
 * @property array $author
 * @property array $topic
 * @property int $limit
 */
abstract class BaseContentRecordsElement extends BaseRecordsElement
{
    /**
     * @inheritdoc
     */
    protected function getDynamicAttributes(): array
    {
        return array_merge(parent::getDynamicAttributes(), [
            'space' => null,
            'author' => null,
            'topic' => null,
            'limit' => null,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'space' => Yii::t('CustomPagesModule.base', 'Spaces'),
            'author' => Yii::t('CustomPagesModule.base', 'Authors'),
            'topic' => Yii::t('CustomPagesModule.base', 'Topics'),
            'limit' => Yii::t('CustomPagesModule.base', 'Limit'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return array_merge(parent::attributeHints(), [
            'space' => Yii::t('CustomPagesModule.base', 'Leave empty to list all records related to the current user.'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getTypes(): array
    {
        return array_merge(parent::getTypes(), [
            'options' => Yii::t('CustomPagesModule.base', 'Specific criteria'),
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function isConfigured(): bool
    {
        return parent::isConfigured() || $this->type === 'options';
    }

    /**
     * @inheritdoc
     */
    public function isCacheable(): bool
    {
        // Don't cache because the filter `ContentActiveRecord::find()->readable()` is used here
        return true;
    }

    /**
     * @inheritdoc
     * @return ActiveQueryContent
     */
    protected function getQuery(): ActiveQuery
    {
        $query = static::RECORD_CLASS::find()->readable();

        return match ($this->type) {
            'options' => $this->filterOptions($query),
            default => $this->filterStatic($query),
        };
    }

    /**
     * @inheritdoc
     */
    protected function filterStatic(ActiveQuery $query): ActiveQuery
    {
        return $query->andWhere(['content.object_id' => $this->static]);
    }

    /**
     * @param ActiveQueryContent $query
     * @return ActiveQuery
     */
    protected function filterOptions(ActiveQueryContent $query): ActiveQuery
    {
        $query->userRelated([
            ActiveQueryContent::USER_RELATED_SCOPE_OWN_PROFILE,
            ActiveQueryContent::USER_RELATED_SCOPE_SPACES,
            ActiveQueryContent::USER_RELATED_SCOPE_OWN,
            ActiveQueryContent::USER_RELATED_SCOPE_FOLLOWED_SPACES,
            ActiveQueryContent::USER_RELATED_SCOPE_FOLLOWED_USERS,
        ]);

        if (!empty($this->space)) {
            $query->andWhere(['space.guid' => $this->space]);
        }

        if (!empty($this->author)) {
            $query->andWhere(['user.guid' => $this->author]);
        }

        if (!empty($this->topic)) {
            $query->leftJoin('content_tag_relation', 'content_tag_relation.content_id = content.id')
                ->andWhere(['content_tag_relation.tag_id' => $this->topic]);
        }

        return $query;
    }
}
