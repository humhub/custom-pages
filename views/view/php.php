<?php

use yii\helpers\Html;
use yii\base\ViewNotFoundException;

/** @var $page \humhub\modules\custom_pages\models\Page */
/** @var $this \humhub\components\View */

$cssClass = ($page->hasAttribute('cssClass') && !empty($page->cssClass)) ? $page->cssClass : 'custom-pages-page';
?>



<div class="<?= Html::encode($cssClass) ?>">
    <?php try { ?>
        <?= $this->renderFile($page->getPhpViewFilePath()) ?>
    <?php } catch(ViewNotFoundException $vnfe) { ?>
        <?= Yii::t('CustomPagesModule.view_php', 'View not found') ?>
    <?php } ?>
</div>