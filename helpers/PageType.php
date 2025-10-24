<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\helpers;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\space\models\Space;
use Yii;

class PageType
{
    public const Page = 'page';
    public const Snippet = 'snippet';

    public const TARGET_TOP_MENU = 'TopMenuWidget';
    public const TARGET_ACCOUNT_MENU = 'AccountMenuWidget';
    public const TARGET_DIRECT_LINK = 'WithOutMenu';
    public const TARGET_FOOTER = 'FooterMenuWidget';
    public const TARGET_START_PAGE = 'StartPage';
    public const TARGET_PEOPLE = 'PeopleButtonsWidget';
    public const TARGET_SPACE_MENU = 'SpaceMenu';
    public const TARGET_DASHBOARD_SIDEBAR = 'Dashboard';
    public const TARGET_SPACE_STREAM_SIDEBAR = 'SpaceStreamSidebar';

    public static function getContentName(string $type, ?string $containerClass = null): ?string
    {
        return match ($type) {
            self::Page => $containerClass === Space::class
                ? Yii::t('CustomPagesModule.base', 'Space Page')
                : Yii::t('CustomPagesModule.base', 'Global Page'),
            self::Snippet => $containerClass === Space::class
                ? Yii::t('CustomPagesModule.base', 'Space Widget')
                : Yii::t('CustomPagesModule.base', 'Global Widget'),
            default => null,
        };
    }

    /**
     * Returns targets where a Custom Page can be located.
     *
     * @param string $type
     * @param ContentContainerActiveRecord|null $container
     * @return array
     */
    public static function getDefaultTargets(string $type, ?ContentContainerActiveRecord $container): array
    {
        if ($type === self::Page) {
            // Space Page
            if ($container instanceof ContentContainerActiveRecord) {
                return [
                    self::TARGET_SPACE_MENU => Yii::t('CustomPagesModule.base', 'Space Navigation'),
                    self::TARGET_DIRECT_LINK => Yii::t('CustomPagesModule.base', 'Without adding to navigation (Direct link)'),
                ];
            }

            // Global Page
            $targets = [
                self::TARGET_TOP_MENU => Yii::t('CustomPagesModule.base', 'Top Navigation'),
                self::TARGET_ACCOUNT_MENU => [
                    'name' => Yii::t('CustomPagesModule.base', 'User Account Menu (Settings)'),
                    'subLayout' => '@humhub/modules/user/views/account/_layout',
                ],
                self::TARGET_DIRECT_LINK => Yii::t('CustomPagesModule.base', 'Without adding to navigation (Direct link)'),
                self::TARGET_FOOTER => Yii::t('CustomPagesModule.base', 'Footer menu'),
                self::TARGET_START_PAGE => Yii::t('CustomPagesModule.base', 'Start Page'),
            ];
            if (class_exists('humhub\modules\user\widgets\PeopleHeadingButtons')) {
                $targets[self::TARGET_PEOPLE] = Yii::t('CustomPagesModule.base', 'People Buttons');
            }

            return $targets;
        }

        if ($type === self::Snippet) {
            // Space Snippet
            if ($container instanceof ContentContainerActiveRecord) {
                return [
                    self::TARGET_SPACE_STREAM_SIDEBAR => [
                        'name' => Yii::t('CustomPagesModule.base', 'Stream'),
                        'accessRoute' => '/space/space/home',
                        'type' => self::Snippet,
                    ],
                ];
            }

            // Global Snippet
            return [
                self::TARGET_DASHBOARD_SIDEBAR => [
                    'name' => Yii::t('CustomPagesModule.base', 'Dashboard'),
                    'accessRoute' => '/dashboard',
                    'type' => self::Snippet,
                ],
            ];
        }

        return [];
    }
}
