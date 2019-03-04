<?php

namespace humhub\modules\custom_pages\modules\template\assets;

use yii\web\AssetBundle;

class InlineEditorAsset extends AssetBundle
{
    public $sourcePath = '@custom_pages/modules/template/resources';

    public $publishOptions = [
        'forceCopy' => false
    ];
    public $jsOptions = ['position' => \yii\web\View::POS_END];
    public $js = [
        'js/humhub.custom_pages.template.editor.js',
        'js/humhub.custom_pages.template.TemplateElement.js',
        'js/humhub.custom_pages.template.TemplateContainer.js',
        'js/humhub.custom_pages.template.TemplateContainerItem.js'
    ];
    public $depends = [
        'humhub\modules\custom_pages\modules\template\assets\TemplateCoreAsset',
        'humhub\modules\custom_pages\modules\template\assets\SwitchAssetBundle'
    ];
}
