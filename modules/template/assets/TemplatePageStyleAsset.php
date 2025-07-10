<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\assets;

use yii\web\AssetBundle;

class TemplatePageStyleAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@custom_pages/modules/template/resources';

    /**
     * @inheritdoc
     */
    public $css = [
        'css/template-pages.css',
    ];

    /**
     * @inheritdoc
     */
    public $publishOptions = [
        'forceCopy' => false,
    ];

}
