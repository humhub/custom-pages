<?php

namespace humhub\modules\custom_pages\modules\template\assets;

use yii\web\AssetBundle;

class InlineEditorAsset extends AssetBundle
{    
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

    public function init()
    {
        $this->sourcePath = dirname(dirname(__FILE__)) . '/resources';
        parent::init();
    }

}
