<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\modules\template\services\TemplateInstanceRendererService;
use humhub\widgets\bootstrap\Button;
use humhub\widgets\bootstrap\Link;

/* @var CustomPage $page */
/* @var ContentContainerActiveRecord $container */
?>
<div style="margin-bottom:5px">
<?php if (TemplateInstanceRendererService::inEditMode()) : ?>
    <div id="editPageButton" class="btn-group">
        <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-pencil"></i>
        </button>
        <ul class="dropdown-menu">
            <?php if ($page->canEdit()) : ?>
            <li>
                <?= Link::to(
                    Yii::t('CustomPagesModule.view', 'Page configuration'),
                    Url::toEditPage($page, $container),
                )->blank()->cssClass('dropdown-item') ?>
            </li>
            <?php endif ?>
            <li>
                <?= Link::to(
                    Yii::t('CustomPagesModule.view', 'Exit Edit Mode'),
                    Url::toViewPage($page, $container),
                )->cssClass('dropdown-item')->pjax(false) ?>
            </li>
        </ul>
    </div>
<?php else: ?>
    <?= Button::primary(Yii::t('CustomPagesModule.template', 'Edit Page'))
        ->icon('pencil')
        ->link(Url::toInlineEdit($page, $container))
        ->id('editPageButton')
        ->sm() ?>
<?php endif; ?>
</div>
