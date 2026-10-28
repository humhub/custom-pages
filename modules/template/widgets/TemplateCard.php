<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\widgets;

use humhub\helpers\Html;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\widgets\ContentCard;
use Yii;

/**
 * The card of a template's pages (General, Source, Resources, Usage): the template's name as
 * title with a back arrow to the templates, its ⋮ menu ({@see TemplateMenu}) and its tabs
 * ({@see TemplateTabs}), and for a default template a hint that it can only be copied.
 *
 * ```php
 * <?php TemplateCard::begin(['model' => $model, 'description' => '…']) ?>
 *     …
 * <?php TemplateCard::end() ?>
 * ```
 */
class TemplateCard extends ContentCard
{
    /**
     * @var Template the template
     */
    public Template $model;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->title = $this->getTemplateTitle();
        $this->backUrl = ['/custom_pages/template/admin/index'];
        $this->backLabel = Yii::t('CustomPagesModule.base', 'Back to overview');

        if (!$this->model->isNewRecord) {
            $this->menu = new TemplateMenu(['model' => $this->model]);
            $this->tabs = new TemplateTabs(['model' => $this->model]);
        }

        parent::init();

        if ($this->model->is_default && !$this->model->isNewRecord) {
            echo Html::tag('div', Yii::t('CustomPagesModule.template', 'This is a default system template and cannot be modified. To make changes, please create a copy.'), [
                'class' => 'alert alert-warning',
            ]);
        }
    }

    /**
     * @inheritdoc
     * The content card's view of the core.
     */
    public function getViewPath()
    {
        return Yii::getAlias('@humhub/widgets/views');
    }

    private function getTemplateTitle(): string
    {
        if ($this->model->isNewRecord) {
            return $this->model->id
                ? Yii::t('CustomPagesModule.template', 'Copying {type}', ['type' => Template::getTypeTitle($this->model->type)])
                : Yii::t('CustomPagesModule.template', 'Creating new Template');
        }

        return $this->model->canEdit()
            ? Yii::t('CustomPagesModule.template', 'Editing: {templateName}', ['templateName' => $this->model->name])
            : Yii::t('CustomPagesModule.template', 'Viewing: {templateName}', ['templateName' => $this->model->name]);
    }
}
