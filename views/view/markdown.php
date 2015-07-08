<?php

use module\custom_pages\models\CustomPage;
?>

<?php if ($navigationClass == CustomPage::NAV_CLASS_ACCOUNTNAV): ?>
    <div class="panel panel-default">
        <div class="panel-body">
    <?php echo humhub\widgets\MarkdownView::widget(['markdown' => $md]); ?>
        </div>
    </div>
<?php else: ?>
    <div class="container">
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-body">
    <?php echo humhub\widgets\MarkdownView::widget(['markdown' => $md]); ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>