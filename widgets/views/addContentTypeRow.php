<?php

use humhub\modules\custom_pages\helpers\Url;
use humhub\widgets\bootstrap\Button;
use yii\helpers\Html;
use humhub\modules\content\helpers\ContentContainerHelper;

/** @var \humhub\modules\custom_pages\models\Target $target */
/** @var \humhub\modules\custom_pages\types\ContentType $contentType*/
/** @var string $pageType */
/** @var bool $disabled*/

$contentContainer = ContentContainerHelper::getCurrent();
 
?>
<tr>
    <td class="text-nowrap">
        <?= Html::encode($contentType->getLabel()) ?>
    </td>
    <td>
        <?= Html::encode($contentType->getDescription()) ?>
    </td>
    <td>
        <?= Button::success(Yii::t('CustomPagesModule.base', 'Add'))
            ->link(Url::toAddContentType($target, $pageType,  $contentType->getId(), $contentContainer))
            ->icon('fa-plus')->sm()->id('add-content-type-'.$contentType->getId())
            ->cssClass(($disabled ? 'disabled' : '')) ?>
    </td>
</tr>
