<?php

use humhub\compat\CActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

\humhub\modules\custom_pages\CkEditorAssetBundle::register($this);

/* @var $model humhub\modules\custom_pages\models\Template */

?>
<div class="panel panel-default">
    <div class="panel-heading"><?php echo Yii::t('CustomPagesModule.base', '<strong>Custom</strong> Pages'); ?></div>
    <?= \humhub\modules\custom_pages\widgets\AdminMenu::widget([]); ?>
    <div class="panel-body">
        <?php echo Html::a('<i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;' . Yii::t('base', 'Back to overview'), Url::to(['index']), array('class' => 'btn btn-default pull-right'));
        ?>
        <h4><?php echo Yii::t('CustomPagesModule.views_admin_add', 'Edit template source'); ?></h4>
    </div>

    <ul class="nav nav-tabs tab-sub-menu" id="tabs">
        <li>
            <?php echo Html::a(Yii::t('CustomPagesModule.base', 'General'), Url::to(['/custom_pages/template/edit', 'id' => $model->id])); ?>
        </li>
        <li class="active">
            <?php echo Html::a(Yii::t('CustomPagesModule.base', 'Source'), Url::to(['/custom_pages/template/edit-source', 'id' => $model->id])); ?>
        </li>
    </ul>
    <div class="panel-body">
        <?php $form = CActiveForm::begin(); ?>
        <!-- autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" -->
        <?= $form->field($model, 'source')->textarea(['id' => 'template-form-source', 'rows' => 15, "spellcheck" => "false" ])->label(false); ?>

        
        <div class="clearfix">
            <?php echo Html::submitButton(Yii::t('CustomPagesModule.views_admin_edit', 'Save'), ['class' => 'btn btn-primary', 'data-ui-loader' => ""]); ?>
            <div class="dropdown pull-right">
                <button class="btn btn-success dropdown-toggle" type="button" data-toggle="dropdown">
                    <i aria-hidden="true" class="fa fa-plus"></i>
                    <?php echo Yii::t('CustomPagesModule.base', 'Add Block'); ?>
                    <span class="caret"></span></button>
                <ul class="dropdown-menu" id="addBlockSelect">
                    <?php foreach ($contentTypes as $label => $type) : ?>
                        <li><a href="#" data-content-type="<?= $type ?>"><?= $label ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <br />
        <?php CActiveForm::end(); ?>
        <?= \humhub\modules\custom_pages\widgets\TemplateBlockAdminTable::widget(['template' => $model]) ?>
    </div>
</div>

<script type="text/javascript">
    $('#template-form-description').autosize();
    $('#addBlockSelect a').on('click', function (evt) {
        evt.preventDefault();
        var type = $(this).data('content-type');
        $.ajax('<?= Url::to(['/custom_pages/template/add-template-block']) ?>', {
            dataType: 'json',
            data: {
                templateId: <?= $model->id ?>,
                type: type
            },
            beforeSend: function () {
                //setModalLoader();
            },
            success: function (json) {
                $('#globalModal').html(json.content);
                $('#globalModal').modal('show');
            },
            error: function () {

            }
        });
    });
    
    $(document).on('templateBlockEditSuccess', function(evt, json) {
        $('#globalModal').modal('hide');
        $('#templateBlocks').append(json.content);
        insertBlock('{{ '+json.name+' }}');
    });
    
    function getCaret(el) { 
        if (el.selectionStart) { 
          return el.selectionStart; 
        } else if (document.selection) { 
          el.focus(); 

          var r = document.selection.createRange(); 
          if (r == null) { 
            return 0; 
          } 

          var re = el.createTextRange(), 
              rc = re.duplicate(); 
          re.moveToBookmark(r.getBookmark()); 
          rc.setEndPoint('EndToStart', re); 

          return rc.text.length; 
        }  
        return 0; 
      }

    function insertBlock($txt) {
        var textarea = document.getElementById('template-form-source');
        var currentPos = getCaret(textarea);
        var strLeft = textarea.value.substring(0,currentPos);
        var strMiddle = $txt;
        var strRight = textarea.value.substring(currentPos,textarea.value.length);
        textarea.value = strLeft + strMiddle + strRight;
    }
    
    
</script>