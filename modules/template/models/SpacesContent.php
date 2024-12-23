<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\models;

use humhub\modules\space\models\Membership;
use humhub\modules\space\models\Space;
use Yii;
use yii\db\ActiveQuery;

/**
 * Class UsersContent
 */
class SpacesContent extends RecordsContent
{
    public const RECORD_CLASS = Space::class;
    public static $label = 'Spaces';
    public string $formView = 'spaces';

    /**
     * @inheritdoc
     */
    public function getTypes(): array
    {
        return array_merge(parent::getTypes(), [
            'member' => Yii::t('CustomPagesModule.template', 'Spaces where the user is'),
            'tag' => Yii::t('CustomPagesModule.template', 'Spaces with a specific tag'),
        ]);
    }

    public function getMemberTypes(): array
    {
        return [
            'member' => Yii::t('CustomPagesModule.template', 'Member'),
            'not-member' => Yii::t('CustomPagesModule.template', 'Not member'),
        ];
    }

    public function getTags(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    protected function getQuery(): ActiveQuery
    {
        $query = Space::find()->visible();

        return match ($this->type) {
            'member' => $this->filterMember($query),
            'tag' => $this->filterTag($query),
            default => $this->filterStatic($query),
        };
    }

    protected function filterMember(ActiveQuery $query): ActiveQuery
    {
        $userGuid = $this->options['member'] ?: Yii::$app->user->getGuid();

        if (empty($this->options['memberType']) || empty($userGuid)) {
            return $query->andWhere(false);
        }

        return match ($this->options['memberType']) {
            'member' => $query->leftJoin('space_membership', 'space_membership.space_id = space.id')
                ->leftJoin('user', 'user.id = space_membership.user_id')
                ->andWhere(['user.guid' => $userGuid])
                ->andWhere(['space_membership.status' => Membership::STATUS_MEMBER]),
            'not-member' => $query->andWhere(['NOT IN', 'space.id', Membership::find()
                ->select('space_membership.space_id')
                ->leftJoin('user', 'space_membership.user_id = user.id')
                ->where(['space_membership.status' => Membership::STATUS_MEMBER])
                ->andWhere(['user.guid' => $userGuid])]),
            default => $query->andWhere(false),
        };
    }

    protected function filterTag(ActiveQuery $query): ActiveQuery
    {
        return $query->leftJoin('contentcontainer_tag_relation', 'contentcontainer_tag_relation.contentcontainer_id = space.contentcontainer_id')
            ->leftJoin('contentcontainer_tag', 'contentcontainer_tag.id = contentcontainer_tag_relation.tag_id')
            ->andWhere(['contentcontainer_tag.contentcontainer_class' => Space::class])
            ->andWhere(['contentcontainer_tag.name' => $this->options['tag']]);
    }

    /**
     * @inheritdoc
     */
    protected function isConfigured(): bool
    {
        return parent::isConfigured() || $this->type === 'member';
    }

    /**
     * @inheritdoc
     */
    public function isCacheable(): bool
    {
        return false;
    }
}
