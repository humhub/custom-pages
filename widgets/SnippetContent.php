<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\widgets;

use humhub\widgets\menu\MenuEntry;
use Yii;

/**
 * Snippet
 *
 * @author buddha
 */
class SnippetContent extends \yii\base\Widget
{
    public $model;
    public $content;
    /**
     * @var MenuEntry[] the entries of the snippet card's ⋮ menu
     */
    public $navigation = [];

    public function run()
    {
        return $this->render('snippet', [
            'model' => $this->model,
            'content' => $this->content,
            'navigation' => $this->navigation]);
    }

}
