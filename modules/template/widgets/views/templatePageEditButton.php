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
use humhub\widgets\Button;
use humhub\widgets\Link;

/* @var CustomPage $page */
/* @var ContentContainerActiveRecord $container */
?>
<?php if (TemplateInstanceRendererService::inEditMode()) : ?>
    <div id="editPageButton" class="btn-group">
        <button type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-pencil"></i>&nbsp;&nbsp;<span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            <li>
                <?= Link::to(
                    Yii::t('CustomPagesModule.view', 'Page configuration'),
                    Url::toEditPage($page, $container),
                )->blank() ?>
            </li>
            <li>
                <?= Link::to(
                    Yii::t('CustomPagesModule.view', 'Exit Edit Mode'),
                    Url::toViewPage($page, $container),
                ) ?>
            </li>
        </ul>
    </div>
<?php else: ?>
    <?= Button::primary(Yii::t('CustomPagesModule.template', 'Edit Page'))
        ->icon('pencil')
        ->link(Url::toInlineEdit($page, $container))
        ->id('editPageButton')
        ->xs() ?>
<?php endif; ?>
