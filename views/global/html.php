<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */
use humhub\components\View;
use humhub\modules\custom_pages\assets\InlineStyleAssets;
use humhub\modules\custom_pages\models\CustomPage;
use yii\helpers\Html;

/* @var $this View */
/* @var $page CustomPage */
/* @var $html string */

$cssClass = ($page->hasAttribute('cssClass') && !empty($page->cssClass)) ? $page->cssClass : 'custom-pages-page';

InlineStyleAssets::register($this);
?>
<div class="container <?= Html::encode($cssClass) ?>">
    <div class="row">
        <div class="col-md-12">
            <?= $html ?>
        </div>
    </div>
</div>
