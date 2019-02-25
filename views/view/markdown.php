<?php
use yii\helpers\Html;
use humhub\modules\custom_pages\models\Page;

$cssClass = ($page->hasAttribute('cssClass') && !empty($page->cssClass)) ? $page->cssClass : 'custom-pages-page';
?>

<?php if ($navigationClass == Page::NAV_CLASS_ACCOUNTNAV): ?>
    <div class="panel panel-default <?= Html::encode($cssClass) ?>">
        <div class="panel-body">
            <?php echo humhub\widgets\MarkdownView::widget(['markdown' => $md]); ?>
        </div>
    </div>
<?php else: ?>
    <div class="container <?= Html::encode($cssClass) ?>">
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-body">
                    <?php echo humhub\widgets\MarkdownView::widget(['markdown' => $md]); ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>