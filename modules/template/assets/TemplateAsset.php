<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\assets;

use humhub\modules\custom_pages\assets\Assets;
use yii\web\AssetBundle;
use yii\web\View;

class TemplateAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $publishOptions = [
        'forceCopy' => false,
    ];

    /**
     * @inheritdoc
     */
    public $sourcePath = '@custom_pages/modules/template/resources';

    /**
     * @inheritdoc
     */
    public $jsOptions = ['position' => View::POS_END];

    /**
     * @inheritdoc
     */
    public $js = [
        'js/humhub.custom_pages.template.js',
    ];

    /**
     * @inheritdoc
     */
    public $css = [
        'css/humhub.custom_pages.template.min.css',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        Assets::class,
    ];
}
