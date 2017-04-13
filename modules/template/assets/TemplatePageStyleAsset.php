<?php

namespace humhub\modules\custom_pages\modules\template\assets;

use yii\web\AssetBundle;

class TemplatePageStyleAsset extends AssetBundle
{
    
     public $css = [
        'css/template-pages.css'
    ];

    public function init()
    {
        $this->sourcePath = dirname(dirname(__FILE__)) . '/resources';
        parent::init();
    }

}
