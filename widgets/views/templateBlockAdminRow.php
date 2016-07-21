<?php
/* @var $form humhub\modules\custom_pages\models\forms\TemplateBlockForm */

use yii\helpers\Url;
?>
<tr>
    <td>
        <?= $model->getLabel() ?>
    </td>
    <td>
        <?= $model->name ?>
    </td>

    <td>
        <a class="btn btn-primary btn-xs tt" href="1">
            <i class="fa fa-eye"></i>
        </a> 
        <a class="btn btn-primary btn-xs tt" href="#">
            <i class="fa fa-pencil"></i>
        </a> 
        <a id="deleteBlock_<?= $model->id ?>" class="btn btn-danger btn-xs tt" href="#">
            <i class="fa fa-times"></i>
        </a>
        <script>
            $('#deleteBlock_<?= $model->id ?>').on('click', function(evt) {
                evt.preventDefault();
                var $this = $(this);
                $.post('<?= Url::to(['/custom_pages/template/delete-template-block', 'id' => $model->id]); ?>', {}, function() {
                    $this.closest('tr').remove();
                });
            });
        </script>
    </td>
</tr>
