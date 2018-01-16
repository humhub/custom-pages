<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\widgets;

use humhub\modules\custom_pages\Module;
use Yii;
use humhub\modules\custom_pages\components\Container;

/**
 * Snippet
 *
 * @author buddha
 */
class SnippetWidget extends \yii\base\Widget
{

    public $model;
    public $canEdit = false;
    
    public function run()
    {
        Module::loadTwig();
        $contentContainer = property_exists(Yii::$app->controller, 'contentContainer') ? Yii::$app->controller->contentContainer : null;
        return $this->render('snippet_'.strtolower(Container::getViewName($this->model->type)), [
            'model' => $this->model,
            'contentContainer' => $contentContainer,
            'canEdit' => $this->canEdit
        ]);
    }

}
