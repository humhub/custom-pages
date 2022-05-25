<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\assets;

use humhub\components\assets\AssetBundle;
use humhub\modules\ui\view\components\View;

class TinyMcePluginsAssets extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@custom_pages/resources/tinymce/plugins';

    /**
     * @inheritdoc
     */
    public $js = [
        'codemirror/plugin.min.js'
    ];

    /**
     * @inheritdoc
     */
    public $jsOptions = ['position' => View::POS_END];
}