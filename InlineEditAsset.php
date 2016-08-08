<?php

namespace humhub\modules\custom_pages;

use yii\web\AssetBundle;

class InlineEditAsset extends AssetBundle
{
    public $jsOptions = ['position' => \yii\web\View::POS_END];
    
    public $js = [
        'js/inlineEditor.js'
    ];
    
    public $depends = [
        'humhub\modules\custom_pages\Assets',
        'humhub\modules\custom_pages\CkEditorAssetBundle',
        'humhub\modules\custom_pages\SwitchAssetBundle',
        'humhub\assets\Select2ExtensionAsset'
    ];

    public function init()
    {
        $this->sourcePath = dirname(__FILE__) . '/assets';
        parent::init();
    }

}
