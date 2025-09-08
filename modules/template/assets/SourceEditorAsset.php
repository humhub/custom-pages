<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\assets;

use humhub\modules\ui\form\assets\CodeMirrorAssetBundle;
use Yii;
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
        TemplateAsset::class,
    ];

    /**
     * @inheritdoc
     */
    public static function register($view)
    {
        $view->registerJsConfig('custom_pages.template.source', params: [
            'text' => [
                'warning.beforeunload' => Yii::t('CustomPagesModule.template', "You haven't saved your last changes yet. Do you want to leave without saving?"),
            ],
        ]);

        return parent::register($view);
    }
}
