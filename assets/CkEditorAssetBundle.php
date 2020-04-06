<?php

namespace humhub\modules\custom_pages\assets;

use yii\web\AssetBundle;

class CkEditorAssetBundle extends AssetBundle
{
    /**
     * v1.5 compatibility defer script loading
     *
     * Migrate to HumHub AssetBundle once minVersion is >=1.5
     *
     * @var bool
     */
    public $defer = true;

    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
    public $sourcePath = '@custom_pages/resources/ckeditor';

    public $js = [
        'ckeditor.js'
    ];

}
