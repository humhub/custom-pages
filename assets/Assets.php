<?php

namespace humhub\modules\custom_pages\assets;

use yii\web\AssetBundle;

class Assets extends AssetBundle
{
    public $sourcePath = '@custom_pages/resources';

    public $publishOptions = [
        'forceCopy' => false
    ];


    public $css = [
        'custom-pages.css'
    ];
    
    public $js = [];

}
