<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\components;

use humhub\helpers\DeviceDetectorHelper;
use humhub\modules\admin\permissions\ManageModules;
use humhub\modules\content\components\ActiveQueryContent;
use humhub\modules\content\models\Content;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\permissions\ManagePages;
use humhub\modules\custom_pages\services\SettingService;
use humhub\modules\user\components\PermissionManager;
use humhub\modules\user\models\User;
use Yii;
use yii\db\Expression;
use yii\db\Query;

class ActiveQueryCustomPage extends ActiveQueryContent
{
    /**
     * Restricts the query to pages the current user is allowed to view according to CustomPage's
     * own visibility model (Admin-only / Guest-only / Custom group-, language- or mobile-app-
     * restricted pages). Mirrors {@see \humhub\modules\custom_pages\services\VisibilityService::canView()}, except for its "viewing an
     * attached file" bypass, which is tied to the current route and never applies to a query.
     * Named to avoid ever colliding with a method the parent {@see ActiveQueryContent} (or its
     * own parent) might add in the future.
     *
     * Meant to be combined with - not instead of - `readable()`: for global (container-less)
     * pages `readable()` does not check `content.visibility` at all for a logged-in user,
     * so the extra restrictions added here carry the real weight for
     * Admin-only / Guest-only / Custom global pages. The query must already be joined with
     * `content` (`readable()` does this itself), since the condition references
     * `content.contentcontainer_id`.
     *
     * @param User|int|string|null $user
     * @return $this
     */
    public function filterByVisibility($user = null): self
    {
        if (!$user && !Yii::$app->user->isGuest) {
            $user = Yii::$app->user->getIdentity();
        } elseif ($user !== null && !$user instanceof User) {
            $user = User::findOne(['id' => $user]);
        }

        // A resolved $user (not a guest, and not an unresolvable id) gets its own PermissionManager,
        // so every check below is evaluated for the given $user rather than always for the current
        // session - unlike \humhub\modules\custom_pages\services\VisibilityService::canView(), which only honors $user for its isCustom()
        // branch.
        $isGuest = !$user instanceof User;
        $permissionManager = $isGuest ? null : new PermissionManager(['subject' => $user]);

        if ($permissionManager !== null && $permissionManager->can([ManagePages::class])) {
            // Matches the ManagePages bypass in CustomPageController::canViewPage() - see everything,
            // no need to even build/apply the restriction below.
            return $this;
        }

        $pageTable = CustomPage::tableName();
        $isGlobal = new Expression(Content::tableName() . '.contentcontainer_id IS NULL');

        // Admin-only global pages: allowed for system admins and for holders of
        // ManageModules/ManagePages, matching VisibilityService::canViewAdminOnlyContent(null) -
        // but evaluated for the given $user instead of always the current session.
        $canViewAdminContent = $permissionManager !== null
            && ($user->isSystemAdmin() || $permissionManager->can([ManageModules::class, ManagePages::class]));

        $blocked = ['or'];

        if (!$canViewAdminContent) {
            $blocked[] = ['and', ["$pageTable.visibility" => CustomPage::VISIBILITY_ADMIN], $isGlobal];
        }

        if (!$isGuest) {
            $blocked[] = ['and', ["$pageTable.visibility" => CustomPage::VISIBILITY_GUEST], $isGlobal];
        }

        // CustomPage::VISIBILITY_CUSTOM pages: allowed for the user's language and (for global
        // pages only) one of the user's groups, blocked by the mobile-app restriction outside of
        // an app request. Never true for guests - matches the `isCustom()` branch of
        // VisibilityService::canView().
        if ($isGuest) {
            $customAllowed = new Expression('0=1');
        } else {
            $customAllowed = ['and'];

            if (!DeviceDetectorHelper::isAppRequest()) {
                $customAllowed[] = ['not', $this->pageSettingMatches('mobileApp', ['1'])];
            }

            $customAllowed[] = ['or',
                $this->noPageSetting('language'),
                $this->pageSettingMatches('language', $user->language),
            ];

            $customAllowed[] = ['or',
                ['not', $isGlobal],
                ['or',
                    $this->noPageSetting('group'),
                    $this->pageSettingMatches('group', $user->getGroupUsers()->select('group_id')->column()),
                ],
            ];
        }

        $blocked[] = ['and',
            ["$pageTable.visibility" => CustomPage::VISIBILITY_CUSTOM],
            ['not', $customAllowed],
        ];

        return $this->andWhere(['not', $blocked]);
    }

    private function noPageSetting(string $name): array
    {
        return ['not exists', $this->pageSettingQuery($name)];
    }

    private function pageSettingMatches(string $name, $value): array
    {
        return ['exists', $this->pageSettingQuery($name)->andWhere(['s.value' => $value])];
    }

    private function pageSettingQuery(string $name): Query
    {
        return (new Query())
            ->from(SettingService::TABLE . ' s')
            ->where(new Expression('s.page_id = ' . CustomPage::tableName() . '.id'))
            ->andWhere(['s.name' => $name]);
    }
}
