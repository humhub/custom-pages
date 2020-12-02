<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2020 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\permissions;

use Yii;
use humhub\modules\user\models\User;

/**
 * ManageFiles Permissions
 */
class ManagePages extends \humhub\libs\BasePermission
{

    /**
     * @inheritdoc
     */
    public $defaultAllowedGroups = [
        User::USERGROUP_FRIEND,
        User::USERGROUP_USER,
        User::USERGROUP_SELF,
    ];

    /**
     * @inheritdoc
     */
    protected $fixedGroups = [
        User::USERGROUP_GUEST
    ];

    /**
     * @inheritdoc
     */
    protected $moduleId = 'custom_pages';

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return Yii::t('CustomPagesModule.base', 'Can manage custom pages');
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return Yii::t('CustomPagesModule.base', 'Allows the user to manage custom pages.');
    }

}
