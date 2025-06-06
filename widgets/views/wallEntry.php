<?php

use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\widgets\Button;

/* @var $page CustomPage */
?>
<div class="media">
    <div class="media-body">
        <div data-ui-show-more>
            <?= RichText::output($page->abstract, ['fadeIn' => true])?>
        </div>

        <?= Button::primary(Yii::t('CustomPagesModule.view', 'Open page...'))->link($page->getUrl())->sm() ?>
    </div>
</div>
