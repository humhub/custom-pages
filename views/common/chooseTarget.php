<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\components\View;
use humhub\helpers\Html;
use humhub\modules\custom_pages\helpers\PageType;
use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\custom_pages\models\Target;
use humhub\widgets\Icon;
use humhub\widgets\modal\Modal;
use humhub\widgets\modal\ModalButton;

/* @var $this View */
/* @var $targets Target[] */
/* @var $pageType string */
?>
<?php Modal::beginDialog([
    'title' => $pageType === PageType::Snippet
        ? Yii::t('CustomPagesModule.base', 'Add snippet')
        : Yii::t('CustomPagesModule.base', 'Add page'),
    'footer' => ModalButton::cancel(),
]) ?>
    <p><?= Yii::t('CustomPagesModule.base', 'Where should it be shown?') ?></p>
    <div class="list-group">
        <?php foreach ($targets as $target) : ?>
            <?= Html::a(
                Icon::get($target->getIcon() ?: 'file-o', ['fixedWidth' => true]) . ' ' . Html::encode($target->name),
                Url::toChooseContentType($target, $pageType),
                ['class' => 'list-group-item list-group-item-action', 'data-pjax-prevent' => true],
            ) ?>
        <?php endforeach; ?>
    </div>
<?php Modal::endDialog() ?>
