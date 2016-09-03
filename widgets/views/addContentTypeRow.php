<?php
use yii\helpers\Url;
use yii\helpers\Html;
 
$contentContainer = property_exists(Yii::$app->controller, 'contentContainer') ? Yii::$app->controller->contentContainer : null;

if($contentContainer == null) {
    $addUrl = Url::to(['add']);
} else {
     $addUrl = $contentContainer->createUrl('add');
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
        <?=
        Html::a('<i class="fa fa-plus"></i> ' . Yii::t('CustomPagesModule.base', 'Add'), $addUrl, 
            ['id' => 'add-'.$type, 'class' => $buttonClass, 'data-ui-loader' => '',
            'data' => [
                'method' => 'post',
                'params' => [
                    'type' => $type
        ]]]);
        ?>
    </td>
</tr>