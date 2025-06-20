<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\post\models\Post;
use Yii;

/**
 * Class to manage content records of the Post elements
 */
class PostElement extends BaseContentRecordElement
{
    protected const RECORD_CLASS = Post::class;

    /**
     * @inheritdoc
     */
    public function getLabel(): string
    {
        return Yii::t('PostModule.base', 'Post');
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'contentRecordId' => Yii::t('CustomPagesModule.base', 'Enter Post ID'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->getRecord()?->message ?? '';
    }

}
