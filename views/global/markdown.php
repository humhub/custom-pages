<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\libs\Html;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\custom_pages\assets\InlineStyleAssets;
use humhub\modules\custom_pages\models\Page;
use humhub\modules\custom_pages\models\PageType;
use humhub\modules\custom_pages\modules\template\widgets\PageConfigurationButton;
use humhub\modules\ui\view\components\View;

/* @var $this View */
/* @var $page Page */

$cssClass = ($page->hasAttribute('cssClass') && !empty($page->cssClass)) ? $page->cssClass : 'custom-pages-page';

InlineStyleAssets::register($this);
?>
<?php if ($page->hasTarget(PageType::TARGET_ACCOUNT_MENU)): ?>
    <div class="panel panel-default <?= Html::encode($cssClass) ?>">
        <div class="panel-body">
            <?= PageConfigurationButton::widget() ?>
            <div class="markdown-render">
                <?= RichText::output($md, ['fadeIn' => true]) ?>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="container <?= Html::encode($cssClass) ?>">
        <div class="row">
            <div class="col-md-12">
                <?= PageConfigurationButton::widget() ?>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="markdown-render">
                            <?= RichText::output($md, ['fadeIn' => true]) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
