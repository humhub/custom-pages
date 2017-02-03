<?php

namespace humhub\modules\custom_pages;

use Yii;
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
        'humhub\modules\custom_pages\SwitchAssetBundle'     
    ];

    public function init()
    {
        $this->sourcePath = dirname(__FILE__) . '/assets';
        if(version_compare(Yii::$app->version, '1.2', '>=')) {
            $this->js[] = 'js/humhub.custom_pages.js';
        } else {
            $this->depends[] = 'humhub\assets\Select2ExtensionAsset';
        }
        parent::init();
    }

}
