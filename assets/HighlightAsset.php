<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\assets;

use humhub\components\assets\AssetBundle;
use Yii;

class HighlightAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@custom_pages/resources';

    /**
     * @inheritdoc
     */
    public $js = [
        'js/humhub.custom_pages.highlight.js',
    ];

    /**
     * @inheritdoc
     */
    public static function register($view)
    {
        $highlight = Yii::$app->request->get('highlight');
        if ($highlight !== null && $highlight !== '') {
            $view->registerJsConfig('custom_pages.highlight', ['highlight' => $highlight]);
        }

        return parent::register($view);
    }
}
