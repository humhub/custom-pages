<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use Exception;
use humhub\modules\activity\interfaces\ConfigurableActivityInterface;
use humhub\modules\activity\models\Activity;
use humhub\modules\activity\services\ActivityManager;
use humhub\modules\activity\services\RenderService;
use Yii;
use yii\db\ActiveRecord;

class ActivityElementVariable extends BaseContentRecordElementVariable
{
    public ?string $title = null;
    public ?string $message = null;
    public ?string $html = null;

    public function setRecord(?ActiveRecord $record): BaseRecordElementVariable
    {
        if ($record instanceof Activity) {
            try {
                $baseActivity = ActivityManager::load($record);

                if ($baseActivity instanceof ConfigurableActivityInterface) {
                    $this->title = $baseActivity->getTitle();
                }

                $renderService = new RenderService($record);

                $this->message = $baseActivity->asHtml();
                $this->html = $renderService->getWeb();
            } catch (Exception $e) {
                Yii::error('Activity not found: ' . $e, 'custom-pages');
            }
        }

        return parent::setRecord($record);
    }
}
