<?php

namespace humhub\modules\custom_pages;

use yii\web\AssetBundle;

class Assets extends AssetBundle
{

    public $css = [
        'bootstrap-select.min.css',
    ];
    public $js = [
        'bootstrap-select.min.js',
    ];

    public function init()
    {
        $this->sourcePath = dirname(__FILE__) . '/assets';
        parent::init();
    }

}
