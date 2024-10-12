<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\custom_pages\assets\InlineStyleAssets;
use humhub\modules\custom_pages\models\Page;
use humhub\modules\custom_pages\modules\template\widgets\PageConfigurationButton;
use humhub\modules\ui\view\components\View;
use yii\helpers\Html;

/* @var $this View */
/* @var $page Page */
/* @var $md string */

$cssClass = ($page->hasAttribute('cssClass') && !empty($page->cssClass)) ? $page->cssClass : 'custom-pages-page';

InlineStyleAssets::register($this);
?>
<?= PageConfigurationButton::widget() ?>
<div class="panel panel-default <?= Html::encode($cssClass) ?>">
    <div class="panel-body">
        <div class="markdown-render">
            <?= RichText::output($md, ['fadeIn' => true]) ?>
        </div>
    </div>
</div>
