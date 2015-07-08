<?php

namespace module\custom_pages;

use yii\web\AssetBundle;

class Assets extends AssetBundle
{

    public $sourcePath = '@module/custom_pages/assets';
    public $css = [
        'bootstrap-select.min.css',
    ];
    public $js = [
        'bootstrap-select.min.js',
    ];

}
