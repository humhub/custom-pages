<?php

namespace humhub\modules\custom_pages;

use yii\web\AssetBundle;

class CkEditorAssetBundle extends AssetBundle
{
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];

    public $js = [
        'ckeditor.js'
    ];

    public function init()
    {
        $this->sourcePath = dirname(__FILE__) . '/assets/ckeditor';
        parent::init();
    }

}
