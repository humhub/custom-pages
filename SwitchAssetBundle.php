<?php

namespace humhub\modules\custom_pages;

use yii\web\AssetBundle;

class SwitchAssetBundle extends AssetBundle
{
    public $jsOptions = ['position' => \yii\web\View::POS_BEGIN];

    public $css = [
        'css/bootstrap3/bootstrap-switch.min.css'
    ];
    
    public $js = [
        'js/bootstrap-switch.min.js'
    ];
    
    public $depends = [
        'humhub\assets\AppAsset'
    ];

    public function init()
    {
        $this->sourcePath = dirname(__FILE__) . '/assets/switch';
        parent::init();
    }

}
