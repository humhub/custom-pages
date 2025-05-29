<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\commands;

use humhub\modules\custom_pages\modules\template\services\TemplateImportService;
use yii\console\Controller;

class ImportController extends Controller
{
    /**
     * Refresh default templates from all active modules
     */
    public function actionRefreshDefaultTemplates()
    {
        TemplateImportService::instance()->importDefaultTemplates();
    }
}
