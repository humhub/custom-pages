<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\assets;

use humhub\components\assets\AssetBundle;
use yii\web\View;

class InlineEditorAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@custom_pages/modules/template/resources';

    /**
     * @inheritdoc
     */
    public $forceCopy = false;

    /**
     * @inheritdoc
     */
    public $jsOptions = ['position' => View::POS_END];

    /**
     * @inheritdoc
     */
    public $js = [
        'js/humhub.custom_pages.template.editor.js',
        'js/humhub.custom_pages.template.structure.js',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        TemplateAsset::class,
    ];
}
