<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\widgets;

use humhub\components\Widget;
use humhub\modules\custom_pages\modules\template\models\OwnerContent;
use humhub\modules\custom_pages\modules\template\models\PagePermission;
use humhub\modules\custom_pages\modules\template\models\TemplateContentActiveRecord;
use yii\helpers\Url;

class DeleteContentButton extends Widget
{
    public ?TemplateContentActiveRecord $model = null;
    public string $previewId = '';

    /**
     * @inheritdoc
     */
    public function beforeRun()
    {
        return parent::beforeRun() && $this->canDelete();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('deleteContentButton', [
            'url' => Url::to(['/custom_pages/template/owner-content/delete-by-content']),
            'options' => $this->getOptions(),
        ]);
    }

    private function canDelete(): bool
    {
        if (!$this->model instanceof TemplateContentActiveRecord) {
            return false;
        }

        if ($this->model->isNewRecord) {
            return false;
        }

        $ownerContent = OwnerContent::findByContent($this->model);

        if (!$ownerContent || $ownerContent->isDefault() || $ownerContent->isEmpty()) {
            return false;
        }

        return PagePermission::canEdit();
    }

    private function getOptions(): array
    {
        return ['data' => [
            'placement' => 'bottom',
            'owner-content' => get_class($this->model),
            'owner-content-id' => $this->model->id,
            'preview-id' => $this->previewId,
        ]];
    }
}
