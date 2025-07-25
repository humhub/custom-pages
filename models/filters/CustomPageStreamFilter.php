<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\models\filters;

use humhub\modules\content\models\Content;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\services\SettingService;
use humhub\modules\custom_pages\services\VisibilityService;
use humhub\modules\stream\models\filters\StreamQueryFilter;
use humhub\modules\user\models\User;
use Yii;

class CustomPageStreamFilter extends StreamQueryFilter
{
    public function apply()
    {
        $this->query->leftJoin(CustomPage::tableName(), CustomPage::tableName() . '.id = ' . Content::tableName() . '.object_id AND ' .
            Content::tableName() . '.object_model = :customPageClassName', ['customPageClassName' => CustomPage::class]);

        $adminCondition = ['AND', [CustomPage::tableName() . '.visibility' => CustomPage::VISIBILITY_ADMIN]];
        // TODO: Try to convert the method to SQL queries to make space custom pages visible even on Dashboard
        if (!VisibilityService::canViewAdminOnlyContent()) {
            $guestCondition[] = 'FALSE';
        }

        $guestCondition = ['AND', [CustomPage::tableName() . '.visibility' => CustomPage::VISIBILITY_GUEST]];
        if (!Yii::$app->user->isGuest) {
            $guestCondition[] = 'FALSE';
        }

        $customCondition = ['AND', [CustomPage::tableName() . '.visibility' => CustomPage::VISIBILITY_CUSTOM]];
        if (Yii::$app->user->isGuest) {
            $customCondition[] = 'FALSE';
        } else {
            /* @var User $user */
            $user = Yii::$app->user->getIdentity();
            $this->query->leftJoin(SettingService::TABLE, SettingService::TABLE . '.page_id = ' . CustomPage::tableName() . '.id');
            // TODO: This condition is not correct yet:
            //       It must search page which has Group AND Language,
            //       but currently it searches Group OR Language
            $customCondition[] = [
                'OR', // TODO: Changing to AND doesn't solve the issue
                [
                    'AND',
                    [SettingService::TABLE . '.name' => 'language'],
                    [SettingService::TABLE . '.value' => $user->language],
                ],
                [
                    'AND',
                    [SettingService::TABLE . '.name' => 'group'],
                    [SettingService::TABLE . '.value' => $user->getGroupUsers()->select('group_id')->column()],
                ],
            ];
        }

        $this->query->andWhere([
            'OR',
            // Apply all below conditions only for Custom Page records
            ['!=', Content::tableName() . '.object_model', CustomPage::class],
            [CustomPage::tableName() . '.visibility' => CustomPage::VISIBILITY_PUBLIC],
            [CustomPage::tableName() . '.visibility' => CustomPage::VISIBILITY_PRIVATE],
            $adminCondition,
            $guestCondition,
            $customCondition,
        ]);
    }
}
