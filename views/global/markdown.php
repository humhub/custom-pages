<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\components\View;
use humhub\helpers\Html;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\custom_pages\assets\Assets;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\helpers\PageType;
use humhub\modules\custom_pages\modules\template\widgets\PageConfigurationButton;

/* @var $this View */
/* @var $page CustomPage */

$cssClass = ($page->hasAttribute('cssClass') && !empty($page->cssClass)) ? $page->cssClass : 'custom-pages-page';

Assets::register($this);
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
