<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\assets;

use humhub\components\assets\AssetBundle;

class InlineStyleAssets extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@custom_pages/resources';

    /**
     * @inheritdoc
     */
    public $css = [
        'css/inline.css',
    ];
}
