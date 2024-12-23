<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\models;

use humhub\modules\user\models\Group;
use humhub\modules\user\models\User;
use Yii;
use yii\db\ActiveQuery;

/**
 * Class UsersContent
 */
class UsersContent extends RecordsContent
{
    public const RECORD_CLASS = User::class;
    public static $label = 'Users';
    public string $formView = 'users';

    /**
     * @inheritdoc
     */
    public function getTypes(): array
    {
        return array_merge(parent::getTypes(), [
            'group' => Yii::t('CustomPagesModule.template', 'Users from the selected group'),
            'friend' => Yii::t('CustomPagesModule.template', 'Users where the user is friend of'),
        ]);
    }

    public function getGroupOptions(): array
    {
        $groups = Yii::$app->user->isAdmin()
            ? Group::find()->all()
            : Group::findAll(['is_admin_group' => '0']);

        $options = [];
        foreach ($groups as $group) {
            $options[$group->id] = $group->name . ($group->is_default_group ? ' (' . Yii::t('AdminModule.base', 'Default') . ')' : '');
        }

        return $options;
    }

    /**
     * @inheritdoc
     */
    protected function getQuery(): ActiveQuery
    {
        $query = User::find()->visible();

        return match ($this->type) {
            'group' => $this->filterGroup($query),
            'friend' => $this->filterFriend($query),
            default => $this->filterStatic($query),
        };
    }

    protected function filterGroup(ActiveQuery $query): ActiveQuery
    {
        return $query->leftJoin('group_user', 'group_user.user_id = user.id')
            ->andWhere(['group_user.group_id' => $this->options['group']]);
    }

    protected function filterFriend(ActiveQuery $query): ActiveQuery
    {
        $friendGuid = $this->options['friend'] ?: Yii::$app->user->getGuid();

        if (empty($friendGuid)) {
            return $query->andWhere(false);
        }

        return $query->leftJoin('user_friendship', 'user_friendship.user_id = user.id')
            ->leftJoin('user AS friend', 'user_friendship.friend_user_id = friend.id')
            ->andWhere(['friend.guid' => $friendGuid]);
    }

    /**
     * @inheritdoc
     */
    protected function isConfigured(): bool
    {
        return parent::isConfigured() || $this->type === 'friend';
    }

    /**
     * @inheritdoc
     */
    public function isCacheable(): bool
    {
        return false;
    }
}
