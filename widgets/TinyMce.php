<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\widgets;

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

        $this->language = $this->language ?? Yii::$app->language;

        $this->clientOptions = ArrayHelper::merge([
            'plugins' => ['code', 'autolink', 'link', 'image', 'lists', 'fullscreen', 'table', 'wordcount'],
            'toolbar' => 'undo redo | blocks | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | code'
        ], $this->clientOptions);
    }
}