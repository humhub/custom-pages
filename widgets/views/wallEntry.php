<?php

use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\widgets\bootstrap\Button;

/* @var $page CustomPage */
?>
<?php if ($page->canView()) : ?>
<div class="media">
    <div class="media-body">
        <div data-ui-show-more>
            <?= RichText::output($page->abstract, ['fadeIn' => true])?>
        </div>

        <?= Button::primary(Yii::t('CustomPagesModule.view', 'Open page...'))->link($page->getUrl())->sm() ?>
    </div>
</div>
<?php else : ?>
    <?= Yii::t('CustomPagesModule.view', 'You don\'t have permission to access the page') ?>
<?php endif; ?>
