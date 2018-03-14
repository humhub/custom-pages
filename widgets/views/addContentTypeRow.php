<?php

//use humhub\widgets\Button;
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
        <?php //Todo: after min version 1.2.2: Button::success(Yii::t('CustomPagesModule.base', 'Add'))->icon('fa-plus')->link($addUrl)->sm()->id('add-'.$type) ?>
        <a id="add-<?= $type ?>" href="<?= $addUrl ?>" data-ui-loader class="<?= $buttonClass ?>">
            <i class="fa fa-plus"></i>  <?=  Yii::t('CustomPagesModule.base', 'Add') ?>
        </a>
    </td>
</tr>