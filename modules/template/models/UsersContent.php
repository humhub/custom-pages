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
}
