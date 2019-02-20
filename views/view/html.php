<?php

use yii\helpers\Html;

/** @var $page \humhub\modules\custom_pages\models\Page */
/** @var $html string */

$cssClass = ($page->hasAttribute('cssClass') && !empty($page->cssClass)) ? $page->cssClass : 'custom-pages-page';
 ?>

<div class="container <?= Html::encode($cssClass) ?>">
    <div class="row">

        <div class="col-md-12">

            <?= $html; ?>

        </div>
    </div>
</div>
