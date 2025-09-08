<?php

use humhub\components\View;
use humhub\modules\custom_pages\models\CustomPage;
use yii\helpers\Html;
use yii\base\ViewNotFoundException;

/* @var $page CustomPage */
/* @var $this View */

$cssClass = ($page->hasAttribute('cssClass') && !empty($page->cssClass)) ? $page->cssClass : 'custom-pages-page';
?>
<div class="<?= Html::encode($cssClass) ?>">
    <?php try { ?>
        <?= $this->renderFile($page->getPhpViewFilePath()) ?>
    <?php } catch(ViewNotFoundException $vnfe) { ?>
        <?= Yii::t('CustomPagesModule.view', 'View not found') ?>
    <?php } ?>
</div>
