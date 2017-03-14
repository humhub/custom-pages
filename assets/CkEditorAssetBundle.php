<?php

namespace humhub\modules\custom_pages\assets;

use yii\web\AssetBundle;

class CkEditorAssetBundle extends AssetBundle
{
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];

    public $js = [
        'ckeditor.js'
    ];

    public function init()
    {
        $this->sourcePath = dirname(dirname(__FILE__)) . '/resources/ckeditor';
        parent::init();
    }

}
