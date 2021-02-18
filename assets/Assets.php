<?php

namespace humhub\modules\custom_pages\assets;

use yii\web\AssetBundle;

class Assets extends AssetBundle
{
    /**
     * v1.5 compatibility defer script loading
     *
     * Migrate to HumHub AssetBundle once minVersion is >=1.5
     *
     * @var bool
     */
    public $defer = true;

    public $sourcePath = '@custom_pages/resources';

    public $publishOptions = [
        'forceCopy' => false,
        'only' => [
            'bgImage1.jpg',
            'bgImage2.jpg',
            'bootstrap-select.css.map',
            'bootstrap-select.min.css',
            'bootstrap-select.js.map',
            'bootstrap-select.min.js',
            'custom-pages.css',
            'loader.gif',
            'module_image.png',
        ]
    ];

    public $css = [
        'custom-pages.css'
    ];

    public $js = [];

}
