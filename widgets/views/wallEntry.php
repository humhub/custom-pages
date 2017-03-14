<?php

use humhub\libs\Helpers;
use humhub\widgets\MarkdownView;
use humhub\modules\custom_pages\components\Container;
?>
<div class="media">
    <div class="media-body">
        <h4 class="media-heading"><a href="<?php echo $page->getUrl(); ?>"><?php echo $page->title; ?></a></h4>

        <?php if ($page->type == Container::TYPE_MARKDOWN) : ?>
            <div class="markdown-render">
                <?php echo MarkdownView::widget(['markdown' => Helpers::truncateText($page->page_content, 500)]); ?>
            </div>
        <?php endif; ?>

        <a href="<?php echo $page->getUrl(); ?>"><?php echo Yii::t('CustomPagesModule.widgets_views_wallentry', 'Open page...'); ?></a>
    </div>
</div>
