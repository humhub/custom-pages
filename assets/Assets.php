<?php

namespace humhub\modules\custom_pages\assets;

use yii\web\AssetBundle;

class Assets extends AssetBundle
{

    public $css = [
        'custom-pages.css'
    ];
    
    public $js = [];

    public function init()
    {
        $this->sourcePath = dirname(dirname(__FILE__)) . '/resources';
        parent::init();
    }

}
