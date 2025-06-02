<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\assets;

use humhub\modules\ui\form\assets\CodeMirrorAssetBundle;
use yii\web\AssetBundle;
use yii\web\View;

class SourceEditorAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@custom_pages/modules/template/resources';

    /**
     * @inheritdoc
     */
    public $jsOptions = ['position' => View::POS_END];

    /**
     * @inheritdoc
     */
    public $js = [
        'js/humhub.custom_pages.template.source.js',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        CodeMirrorAssetBundle::class,
        TemplateCoreAsset::class,
    ];
}
