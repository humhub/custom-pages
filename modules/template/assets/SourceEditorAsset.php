<?php

namespace humhub\modules\custom_pages\modules\template\assets;

use humhub\modules\ui\form\assets\CodeMirrorAssetBundle;
use yii\web\AssetBundle;

class SourceEditorAsset extends AssetBundle
{
    public $sourcePath = '@custom_pages/modules/template/resources';

    public $jsOptions = ['position' => \yii\web\View::POS_END];
    
    public $js = [
        'js/humhub.custom_pages.template.source.js'
    ];
    
    public $depends = [
        CodeMirrorAssetBundle::class,
        TemplateCoreAsset::class
    ];
}
