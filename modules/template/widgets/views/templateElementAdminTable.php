<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\custom_pages\modules\template\elements\BaseElementContent;
use humhub\modules\custom_pages\modules\template\widgets\TemplateElementAdminRow;

/* @var $elements BaseElementContent */
?>
<div id="templateElementTable" class="grid-view" style="padding:0">
    <table class="table table-hover">
        <thead<?= $elements === [] ? ' style="display:none"' : '' ?>>
            <tr>
                <th><?= Yii::t('CustomPagesModule.template', 'Label') ?></th>
                <th><?= Yii::t('CustomPagesModule.template', 'Placeholder') ?></th>
                <th><?= Yii::t('CustomPagesModule.template', 'Type') ?></th>
                <th style="width:80px"><?= Yii::t('CustomPagesModule.template', 'Action') ?></th>
            </tr>
        </thead>
        <tbody id="templateElements">
            <?php foreach ($elements as $element) : ?>
                <?= TemplateElementAdminRow::widget(['model' => $element]) ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
