<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use yii\base\ViewNotFoundException;
use yii\helpers\Html;

/** @var $page \humhub\modules\custom_pages\models\Page */
/** @var $this \humhub\components\View */
/** @var $contentContainer \humhub\modules\content\components\ContentContainerActiveRecord*/

$cssClass = ($page->hasAttribute('cssClass') && !empty($page->cssClass)) ? $page->cssClass : 'custom-pages-page';
?>


<div class="container <?= Html::encode($cssClass) ?>">
    <div class="row">

        <div class="col-md-12">
            <?php try { ?>
                <?= $this->renderFile($page->getPhpViewFilePath(), ['contentContainer' => $contentContainer]) ?>
            <?php } catch (ViewNotFoundException $vnfe) { ?>
                <?= Yii::t('CustomPagesModule.view_php', 'View not found') ?>
            <?php } ?>
        </div>
    </div>
</div>