<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\components;

use humhub\helpers\Html;
use humhub\libs\ActionColumn;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\widgets\bootstrap\Button;
use Yii;

class TemplateActionColumn extends ActionColumn
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->header = Yii::t('CustomPagesModule.template', 'Actions');
        $this->headerOptions['class'] = 'text-nowrap';
    }

    protected function getTemplateActions(Template $model): array
    {
        $actions = [];

        if ($model->canEdit()) {
            $actions[] = [
                'icon' => 'pencil',
                'title' => Yii::t('CustomPagesModule.template', 'Edit'),
                'url' => ['edit'],
            ];
        } else {
            $actions[] = [
                'icon' => 'eye',
                'title' => Yii::t('CustomPagesModule.template', 'View'),
                'url' => ['edit'],
            ];
        }

        $actions[] = [
            'icon' => 'copy',
            'title' => Yii::t('CustomPagesModule.template', 'Copy'),
            'url' => ['copy'],
        ];

        $actions[] = [
            'icon' => 'upload',
            'title' => Yii::t('CustomPagesModule.template', 'Export'),
            'url' => ['export-source'],
        ];

        if ($model->canDelete()) {
            $actions[] = [
                'icon' => 'trash',
                'title' => Yii::t('CustomPagesModule.template', 'Delete'),
                'url' => ['delete-template'],
                'confirm' => true,
            ];
        }

        return $actions;
    }

    /**
     * @inheritdoc
     * @param Template $model
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $html = Html::beginTag('div', ['class' => 'btn-group dropdown-navigation']);
        $html .= Button::light('<span class="caret"></span>')
            ->cssClass('dropdown-toggle')
            ->sm()
            ->options(['data-toggle' => 'dropdown'])
            ->icon('controls')
            ->loader(false);

        $html .= Html::beginTag('ul', ['class' => 'dropdown-menu pull-right']);
        foreach ($this->getTemplateActions($model) as $action) {
            $html .= Html::tag('li', Html::a(
                Icon::get($action['icon']) . ' ' . $action['title'],
                $this->handleUrl($action['url'], $model),
                !empty($action['confirm']) ? ['data-action-confirm' => ''] : [],
            ));
        }
        $html .= Html::endTag('ul');
        $html .= Html::endTag('div');

        return $html;
    }

}
