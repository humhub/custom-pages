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

    public $optionUsers;
    public $optionGroups;
    public $optionFriend;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['optionUsers', 'optionGroups', 'optionFriend'], 'safe'],
        ]);
    }

    protected function getScenarioAttributes(?string $scenario = null): array
    {
        return array_merge(parent::getScenarioAttributes(), ['optionUsers', 'optionGroups', 'optionFriend']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'optionUsers' => Yii::t('CustomPagesModule.template', 'Select users'),
            'optionGroups' => Yii::t('CustomPagesModule.template', 'Select groups'),
            'optionFriend' => Yii::t('CustomPagesModule.template', 'User'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getTypes(): array
    {
        return array_merge(parent::getTypes(), [
            'group' => Yii::t('CustomPagesModule.template', 'Users from the selected groups'),
            'friend' => Yii::t('CustomPagesModule.template', 'Users where the user is friend of'),
        ]);
    }

    protected function initArrayOptions(): void
    {
        parent::initArrayOptions();
        $this->optionUsers = $this->getArrayOption('users', []);
        $this->optionGroups = $this->getArrayOption('groups', []);
        $this->optionFriend = $this->getArrayOption('friend', []);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $this->setArrayOption('users', $this->optionUsers);
        $this->setArrayOption('groups', $this->optionGroups);
        $this->setArrayOption('friend', $this->optionFriend);

        return parent::beforeSave($insert);
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
