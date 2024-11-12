<?php

namespace humhub\modules\custom_pages\models;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\space\models\Space;
use Yii;

abstract class PageType
{
    public const Page = 'page';
    public const Snippet = 'snippet';

    public const TARGET_TOP_MENU = 'TopMenuWidget';
    public const TARGET_ACCOUNT_MENU = 'AccountMenuWidget';
    public const TARGET_DIRECT_LINK = 'WithOutMenu';
    public const TARGET_FOOTER = 'FooterMenuWidget';
    public const TARGET_PEOPLE = 'PeopleButtonsWidget';
    public const TARGET_SPACE_MENU = 'SpaceMenu';
    public const TARGET_DASHBOARD_SIDEBAR = 'Dashboard';
    public const TARGET_SPACE_SIDEBAR = 'SpaceStreamSidebar';

    public static function getContentName(string $type, ?string $containerClass = null): ?string
    {
        switch ($type) {
            case self::Page:
                return $containerClass === Space::class
                    ? Yii::t('CustomPagesModule.base', 'Space Page')
                    : Yii::t('CustomPagesModule.base', 'Global Page');
            case self::Snippet:
                return $containerClass === Space::class
                    ? Yii::t('CustomPagesModule.base', 'Space Widget')
                    : Yii::t('CustomPagesModule.base', 'Global Widget');
        }

        return null;
    }

    /**
     * Returns targets where a Custom Page can be located.
     *
     * @param string $type
     * @return array
     */
    public static function getDefaultTargets(string $type, ?ContentContainerActiveRecord $container): array
    {
        if ($type === self::Page) {
            // Space Page
            if ($container instanceof ContentContainerActiveRecord) {
                return [
                    ['id' => self::TARGET_SPACE_MENU , 'name' => Yii::t('CustomPagesModule.base', 'Space Navigation')],
                    ['id' => self::TARGET_DIRECT_LINK, 'name' => Yii::t('CustomPagesModule.base', 'Without adding to navigation (Direct link)')],
                ];
            }

            // Global Page
            $targets = [
                ['id' => self::TARGET_TOP_MENU, 'name' => Yii::t('CustomPagesModule.base', 'Top Navigation')],
                ['id' => self::TARGET_ACCOUNT_MENU, 'name' => Yii::t('CustomPagesModule.base', 'User Account Menu (Settings)'), 'subLayout' => '@humhub/modules/user/views/account/_layout'],
                ['id' => self::TARGET_DIRECT_LINK, 'name' => Yii::t('CustomPagesModule.base', 'Without adding to navigation (Direct link)')],
                ['id' => self::TARGET_FOOTER, 'name' => Yii::t('CustomPagesModule.base', 'Footer menu')],
            ];
            if (class_exists('humhub\modules\user\widgets\PeopleHeadingButtons')) {
                $targets[] = ['id' => self::TARGET_PEOPLE, 'name' => Yii::t('CustomPagesModule.base', 'People Buttons')];
            }
            return $targets;
        }

        if ($type === self::Snippet) {
            // Space Snippet
            if ($container instanceof ContentContainerActiveRecord) {
                return [
                    [
                        'id' => self::TARGET_SPACE_SIDEBAR,
                        'name' => Yii::t('CustomPagesModule.base', 'Stream'),
                        'accessRoute' => '/space/space/home',
                        'isSnippet' => true,
                    ],
                ];
            }

            // Global Snippet
            return [
                [
                    'id' => self::TARGET_DASHBOARD_SIDEBAR,
                    'name' => Yii::t('CustomPagesModule.base', 'Dashboard'),
                    'accessRoute' => '/dashboard',
                    'isSnippet' => true,
                ],
            ];
        }

        return [];
    }
}
