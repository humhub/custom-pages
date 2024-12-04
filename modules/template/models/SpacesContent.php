<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\models;

use humhub\modules\space\models\Space;
use Yii;

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
            'member' => Yii::t('CustomPagesModule.template', 'Spaces where current user is'),
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
}
