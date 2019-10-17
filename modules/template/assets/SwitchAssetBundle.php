<?php

namespace humhub\modules\custom_pages\modules\template\assets;

use yii\web\AssetBundle;

class SwitchAssetBundle extends AssetBundle
{
    public $sourcePath = '@custom_pages/modules/template/resources/js/switch';

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
}
