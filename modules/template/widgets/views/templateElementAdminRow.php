<?php

use yii\helpers\Html;

/* @var $model humhub\modules\custom_pages\modules\template\models\TemplateElement */

use yii\helpers\Url;
?>
<tr data-template-element-definition="<?= $model->id ?>" >
    <td>
        #<strong><?= Html::encode($model->name) ?> </strong>
    </td>
    <td>
        <small>
            <span class="label label-success"><?= $model->getLabel() ?></span>
        </small>
        <?php if (!$model->hasDefaultContent() || $model->defaultContent->use_default) : ?>
            <small>
                <span class="label label-warning"><?= Yii::t('CustomPagesModule.base', 'Empty') ?></span>
            </small>
        <?php else: ?>
            <small>
                <span class="label btn-success"><?= Yii::t('CustomPagesModule.base', 'Default') ?></span>
            </small>
        <?php endif; ?>
        <?php if ($saved) : ?> 
            <?= \humhub\widgets\DataSaved::widget() ?>
        <?php endif; ?>
    </td>

    <td>
        <a  id="editElement_<?= $model->id ?>" class="btn btn-primary btn-xs tt" href="#">
            <i class="fa fa-pencil"></i>
        </a> 
        <a id="deleteElement_<?= $model->id ?>" class="btn btn-danger btn-xs tt" href="#">
            <i class="fa fa-times"></i>
        </a>

        <script>
            $('#deleteElement_<?= $model->id ?>').on('click', function (evt) {
                evt.preventDefault();
                var $this = $(this);
                $.ajax('<?= Url::to(['/custom_pages/template/admin/delete-element', 'id' => $model->id]); ?>', {
                    method: 'POST',
                    dataType: 'json',
                    success: function (json) {
                        $('#globalModal').html(json.content);
                        $('#globalModal').modal('show');
                    }
                });
            });

            $('#editElement_<?= $model->id ?>').on('click', function (evt) {
                evt.preventDefault();
                $.ajax('<?= Url::to(['/custom_pages/template/admin/edit-element', 'id' => $model->id]); ?>', {
                    method: 'POST',
                    dataType: 'json',
                    beforeSend: function () {
                        setModalLoader();
                        $('#globalModal').modal('show');
                    },
                    success: function (json) {
                        $('#globalModal').html(json.content);
                        $('#globalModal').modal('show');
                    }
                });
            });
        </script>
    </td>
</tr>
