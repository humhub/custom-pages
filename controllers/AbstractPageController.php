<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2019 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\controllers;


use humhub\modules\content\components\ContentContainerController;

abstract class AbstractPageController extends ContentContainerController
{
    /**
     * @inheritdoc
     */
    public $requireContainer = false;


}