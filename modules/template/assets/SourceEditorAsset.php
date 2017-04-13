<?php

namespace humhub\modules\custom_pages\modules\template\assets;

use yii\web\AssetBundle;

class SourceEditorAsset extends AssetBundle
{
    
    public $jsOptions = ['position' => \yii\web\View::POS_END];
    
    public $js = [
        'js/humhub.custom_pages.template.source.js'
    ];
    
    public $depends = [
        'humhub\modules\custom_pages\modules\template\assets\TemplateCoreAsset',
        'humhub\modules\custom_pages\assets\CkEditorAssetBundle',
    ];

    public function init()
    {
        $this->sourcePath = dirname(dirname(__FILE__)) . '/resources';
        parent::init();
    }

}
