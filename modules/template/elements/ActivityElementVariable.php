<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use Exception;
use humhub\modules\activity\components\ActivityWebRenderer;
use humhub\modules\activity\components\BaseActivity;
use humhub\modules\activity\models\Activity;
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
                $baseActivity = $record->getActivityBaseClass();
                if ($baseActivity instanceof BaseActivity) {
                    $this->title = $baseActivity->getTitle();

                    $renderer = new ActivityWebRenderer();
                    $this->message = $renderer->renderView($baseActivity, $baseActivity->getViewParams());
                    $this->html = $renderer->render($baseActivity, ['content' => $this->message]);
                }
            } catch (Exception $e) {
                Yii::error('Activity not found: ' . $e, 'custom-pages');
            }
        }

        return parent::setRecord($record);
    }
}
