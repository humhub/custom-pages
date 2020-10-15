<?php

namespace humhub\modules\custom_pages\assets;

use yii\web\AssetBundle;

class Assets extends AssetBundle
{
    /**
     * v1.5 compatibility defer script loading
     *
     * Migrate to HumHub AssetBundle once minVersion is >=1.5
     *
     * @var bool
     */
    public $defer = true;

    public $sourcePath = '@custom_pages/resources';

    public $publishOptions = [
        'forceCopy' => false
    ];


    public $css = [
        'custom-pages.css'
    ];

    public $js = [];

}
