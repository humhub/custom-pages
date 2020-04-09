<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\widgets;

use Yii;
use humhub\modules\custom_pages\models\CustomContentContainer;
use humhub\modules\custom_pages\Module;
use yii\base\Widget;

/**
 * Snippet
 *
 * @author buddha
 */
class SnippetWidget extends Widget
{

    /**
     * @var CustomContentContainer
     */
    public $model;

    public $canEdit = false;

    public function run()
    {
        $contentContainer = property_exists(Yii::$app->controller, 'contentContainer') ? Yii::$app->controller->contentContainer : null;
        return $this->render('snippet_'.$this->model->getContentType()->getViewName(), [
            'model' => $this->model,
            'contentContainer' => $contentContainer,
            'canEdit' => $this->canEdit
        ]);
    }

}
