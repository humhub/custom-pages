<?php
use yii\helpers\Url;
 
$contentContainer = property_exists(Yii::$app->controller, 'contentContainer') ? Yii::$app->controller->contentContainer : null;

if($contentContainer == null) {
    $addUrl = Url::to(['add', 'type' => $type]);
} else {
     $addUrl = $contentContainer->createUrl('add', ['type' => $type]);
}
 
 $buttonClass = 'btn btn-sm btn-success pull-right';
 
 if($disabled) {
     $buttonClass .= ' disabled';
 }
 
?>
<tr>
    <td><?= $label ?></td>
    <td><p class="help-block"><?= $description ?></p></td>
    <td>
        <a href="<?= $addUrl ?>" data-ui-loader class="<?= $buttonClass ?>">
            <i class="fa fa-plus"></i>  <?=  Yii::t('CustomPagesModule.base', 'Add') ?>
        </button>
    </td>
</tr>