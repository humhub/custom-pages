<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\widgets;

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
    public $navigation = [];
    
    public function run()
    {
        return $this->render('snippet', [
            'model' => $this->model, 
            'content' => $this->content,
            'navigation' => $this->navigation]);
    }

}
