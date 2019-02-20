<?php

use humhub\widgets\Button;
use humhub\modules\custom_pages\helpers\Url;
use yii\helpers\Html;
use humhub\modules\content\helpers\ContentContainerHelper;

/** @var \humhub\modules\custom_pages\models\Target $target */
/** @var \humhub\modules\custom_pages\models\ContentType $contentType*/
/** @var string $pageType */
/** @var bool $disabled*/

$contentContainer = ContentContainerHelper::getCurrent();
 
?>
<tr>
    <td>
        <?= Html::encode($contentType->getLabel()) ?>
    </td>
    <td>
        <p class="help-block">
            <?= Html::encode($contentType->getDescription()) ?>
        </p>
    </td>
    <td>
        <?= Button::success(Yii::t('CustomPagesModule.base', 'Add'))
            ->link(Url::toAddContentType($target, $pageType,  $contentType->getId(), $contentContainer))
            ->icon('fa-plus')->sm()->id('add-content-type-'.$contentType->getId())
            ->cssClass(($disabled ? 'disabled' : '')) ?>
    </td>
</tr>