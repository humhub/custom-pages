<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\widgets;

use humhub\modules\custom_pages\assets\TinyMcePluginsAssets;
use Yii;
use yii\helpers\ArrayHelper;

class TinyMce extends \dosamigos\tinymce\TinyMce
{
    public function init()
    {
        parent::init();
        $this->initDefaults();
    }

    private function initDefaults()
    {
        $this->options = ArrayHelper::merge([
            'rows' => 15
        ], $this->options);

        $this->language = substr($this->language ?? Yii::$app->language, 0, 2);

        $tinyMcePluginsAssets = TinyMcePluginsAssets::register($this->view);
        $external_plugins = [
            'codemirror' => $tinyMcePluginsAssets->baseUrl . '/codemirror/plugin.min.js',
            'wrapper' => $tinyMcePluginsAssets->baseUrl . '/wrapper/plugin.min.js',
        ];
        $humhubTriggerToolbar = '';
        if (isset($this->clientOptions['humhubTrigger'])) {
            $external_plugins['humhubtrigger'] = $tinyMcePluginsAssets->baseUrl . '/humhubtrigger/plugin.min.js';
            $humhubTriggerToolbar = ' | humhubtrigger';
        }

        $this->clientOptions = ArrayHelper::merge([
            'plugins' => ['code', 'autolink', 'link', 'image', 'lists', 'fullscreen', 'table', 'wordcount', 'anchor', 'lists'],
            'menu' => ['insert' => [
                'title' => Yii::t('CustomPagesModule.base', 'Insert'),
                'items' => 'image humhubtrigger link anchor inserttable | hr'
            ]],
            'toolbar' => 'undo redo | wrapper blocks | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist' . $humhubTriggerToolbar . ' | removeformat | code',
            'content_style' => '.img-responsive {display:block;max-width:100%;height:auto}',
            'valid_elements' => '*[*]',
            'relative_urls' => false,
            'remove_script_host' => true,
            'external_plugins' => $external_plugins,
            'wrapper' => [
                'text' => Yii::t('CustomPagesModule.base', 'Panel'),
                'tooltip' => Yii::t('CustomPagesModule.base', 'Wrap this HTML page with white panel'),
            ]
        ], $this->clientOptions);

        // Fix issue with disabled inputs when it is loaded on modal window
        // Fix the editor initialization on second time loading by modal window(without browser page refreshing)
        $this->view->registerJs('$(document).on("focusin", "[class^=tox-] input", function(e) {e.stopImmediatePropagation()});
            tinymce.remove("#' . $this->options['id'] . '")');
    }
}
