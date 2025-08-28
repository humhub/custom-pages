<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\widgets;

use humhub\modules\content\helpers\ContentContainerHelper;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\modules\template\helpers\PagePermissionHelper;
use yii\base\Widget;

/**
 * Snippet
 *
 * @author buddha
 */
class SnippetWidget extends Widget
{
    /**
     * @var CustomPage
     */
    public $model;

    public function run()
    {
        return $this->render('snippet_' . $this->model->getContentType()->getViewName(), [
            'model' => $this->model,
            'contentContainer' => ContentContainerHelper::getCurrent(),
            'canEdit' => PagePermissionHelper::canEdit($this->model),
        ]);
    }

}
