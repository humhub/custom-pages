<?php

namespace humhub\modules\custom_pages\assets;

use yii\web\AssetBundle;

class CkEditorAssetBundle extends AssetBundle
{
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
    public $sourcePath = '@custom_pages/resources/ckeditor';

    public $js = [
        'ckeditor.js'
    ];

}
