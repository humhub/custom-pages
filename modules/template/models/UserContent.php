<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\models;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\user\models\User;
use Yii;

/**
 * Class UserContent
 */
class UserContent extends ContentContainerContent
{
    public const CONTAINER_CLASS = User::class;
    public static $label = 'User';

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'guid' => Yii::t('CustomPagesModule.template', 'Select user'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
            'guid' => Yii::t('CustomPagesModule.template', 'When no user is selected, the current logged in user will be used.'),
        ];
    }

    /**
     * @inheritdoc
     *
     * @return User|null
     */
    public function getRecord(?string $guid = null): ?ContentContainerActiveRecord
    {
        return parent::getRecord($this->guid ?: Yii::$app->user->getGuid());
    }

    /**
     * Get a profile field
     *
     * @param string|null $field Field name or NULL to get a display name by default
     * @return string
     */
    public function getProfileField(string $field = null): string
    {
        return $this->getRecord() instanceof User
            ? $this->getRecord()->profile->$field ?? $this->getRecord()->displayName
            : '';
    }
}
