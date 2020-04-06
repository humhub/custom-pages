<?php

namespace humhub\modules\custom_pages\modules\template\assets;

use yii\web\AssetBundle;

class TemplateCoreAsset extends AssetBundle
{
    public $publishOptions = [
        'forceCopy' => true
    ];

    public $sourcePath = '@custom_pages/modules/template/resources';
    
    public $jsOptions = ['position' => \yii\web\View::POS_END];
    
    public $js = [
        'js/humhub.custom_pages.template.js'
    ];
    
    public $depends = [
        'humhub\modules\custom_pages\assets\Assets',
        'humhub\modules\custom_pages\assets\CkEditorAssetBundle'
    ];
}
