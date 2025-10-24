<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\components\View;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\custom_pages\models\CustomPage;
use yii\base\ViewNotFoundException;
use yii\helpers\Html;

/* @var $page CustomPage */
/* @var $this View */
/* @var $contentContainer ContentContainerActiveRecord */

$cssClass = ($page->hasAttribute('cssClass') && !empty($page->cssClass)) ? $page->cssClass : 'custom-pages-page';
?>
<div class="container <?= Html::encode($cssClass) ?>">
    <div class="row">

        <div class="col-md-12">
            <?php try { ?>
                <?= $this->renderFile($page->getPhpViewFilePath(), ['contentContainer' => $contentContainer]) ?>
            <?php } catch (ViewNotFoundException) { ?>
                <?= Yii::t('CustomPagesModule.view', 'View not found') ?>
            <?php } ?>
        </div>
    </div>
</div>
