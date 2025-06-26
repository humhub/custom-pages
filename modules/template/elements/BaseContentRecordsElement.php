<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\content\components\ActiveQueryContent;
use humhub\modules\space\widgets\SpacePickerField;
use humhub\modules\stream\models\filters\DefaultStreamFilter;
use humhub\modules\topic\widgets\TopicPicker;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\user\widgets\UserPickerField;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * Abstract class to manage Element Content list of the ContentActiveRecords
 *
 * Dynamic attributes:
 * @property array $space
 * @property array $author
 * @property array $topic
 * @property array $filter
 * @property int $limit
 */
abstract class BaseContentRecordsElement extends BaseRecordsElement
{
    /**
     * @inheritdoc
     */
    protected function getDynamicAttributes(): array
    {
        return [
            'space' => null,
            'author' => null,
            'topic' => null,
            'filter' => null,
            'limit' => null,
        ];
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
            'filter' => Yii::t('CustomPagesModule.base', 'Content filters'),
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
     * @return ActiveQueryContent
     */
    protected function getQuery(): ActiveQuery
    {
        $query = static::RECORD_CLASS::find()->readable();

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

        if (!Yii::$app->user->isGuest) {
            if ($this->hasFilter(DefaultStreamFilter::FILTER_INVOLVED)) {
                $query->leftJoin('user_follow AS user_involved', 'content.object_model = user_involved.object_model AND content.object_id = user_involved.object_id AND user_involved.user_id = :userId', [
                    'userId' => Yii::$app->user->id,
                ]);
                $query->andWhere(['IS NOT', 'user_involved.id', new Expression('NULL')]);
            }

            if ($this->hasFilter(DefaultStreamFilter::FILTER_MINE)) {
                $query->andWhere(['content.created_by' => Yii::$app->user->id]);
            }
        }

        return $query->limit($this->limit);
    }

    /**
     * Get options to filter the content record
     *
     * @return array
     */
    public function getContentFilterOptions(): array
    {
        return [
            DefaultStreamFilter::FILTER_INVOLVED => Yii::t('ContentModule.base', 'I\'m involved'),
            DefaultStreamFilter::FILTER_MINE => Yii::t('ContentModule.base', 'Created by me'),
        ];
    }

    public function hasFilter(string $name): bool
    {
        return is_array($this->filter) && in_array($name, $this->filter);
    }

    /**
     * @inheritdoc
     */
    public function renderEditForm(ActiveForm $form): string
    {
        return $form->field($this, 'space')->widget(SpacePickerField::class) .
            $form->field($this, 'author')->widget(UserPickerField::class) .
            $form->field($this, 'topic')->widget(TopicPicker::class) .
            $form->field($this, 'filter')->checkboxList($this->getContentFilterOptions()) .
            $form->field($this, 'limit');
    }
}
