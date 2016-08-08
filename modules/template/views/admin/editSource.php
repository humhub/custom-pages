<?php

use humhub\compat\CActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

\humhub\modules\custom_pages\CkEditorAssetBundle::register($this);
\humhub\modules\custom_pages\Assets::register($this);
\humhub\assets\Select2ExtensionAsset::register($this);

/* @var $model humhub\modules\custom_pages\modules\template\models\Template */
?>
<div class="panel panel-default">
    <div class="panel-heading"><?php echo Yii::t('CustomPagesModule.base', '<strong>Custom</strong> Pages'); ?></div>

    <?= \humhub\modules\custom_pages\widgets\AdminMenu::widget([]); ?>

    <div class="panel-body">
        <?php echo Html::a('<i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;' . Yii::t('base', 'Back to overview'), Url::to(['index']), ['class' => 'btn btn-default pull-right']); ?>
        <h4><?= Yii::t('CustomPagesModule.modules_template_views_admin_editSource', 'Edit template \'{templateName}\'', ['templateName' => $model->name]); ?></h4>
        <div class="help-block">
            <?=
            Yii::t('CustomPagesModule.modules_template_views_admin_editSource', 'Here you can edit the source of your template by defining the template layout and adding content elements. '
                    . 'Each element can be assigned with a default content and additional definitions.');
            ?>
        </div>
    </div>

    <ul class="nav nav-tabs tab-sub-menu" id="tabs">
        <li>
            <?php echo Html::a(Yii::t('CustomPagesModule.base', 'General'), Url::to(['edit', 'id' => $model->id])); ?>
        </li>
        <li class="active">
            <?php echo Html::a(Yii::t('CustomPagesModule.base', 'Source'), Url::to(['edit-source', 'id' => $model->id])); ?>
        </li>
        <li>
            <?php echo Html::a('<i aria-hidden="true" class="fa fa-question-circle"></i> '.Yii::t('CustomPagesModule.base', 'Help'), 
                    Url::to(['info', 'id' => $model->id])); ?>
        </li>
    </ul>

    <div class="panel-body">

        <?php $form = CActiveForm::begin(['enableClientValidation' => false, 'options' => ['id' => 'sourceForm']]); ?>

        <?= $form->field($model, 'source')->textarea(['id' => 'template-form-source', 'rows' => 15, "spellcheck" => "false"])->label(false); ?>

        <div class="clearfix">
            <?php echo Html::submitButton(Yii::t('CustomPagesModule.base', 'Save'), ['class' => 'btn btn-primary', 'data-ui-loader' => ""]); ?>
            <?= \humhub\widgets\DataSaved::widget([]); ?>
            <div class="dropdown pull-right">
                <button id="editAllElements" class="btn btn-primary" type="button">
                    <i aria-hidden="true" class="fa fa-pencil"></i>
                    <?php echo Yii::t('CustomPagesModule.modules_template_views_admin_editSource', 'Edit All'); ?>
                </button>
                <button class="btn btn-success dropdown-toggle" type="button" data-toggle="dropdown">
                    <i aria-hidden="true" class="fa fa-plus"></i>
                    <?php echo Yii::t('CustomPagesModule.modules_template_views_admin_editSource', 'Add Element'); ?>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" id="addElementSelect">
                    <?php foreach ($contentTypes as $label => $type) : ?>
                        <li><a href="#" data-content-type="<?= $type ?>"><?= $label ?></a></li>
                    <?php endforeach; ?>
                </ul>

            </div>
        </div>
        <br />
        <?php CActiveForm::end(); ?>

        <?= \humhub\modules\custom_pages\modules\template\widgets\TemplateContentTable::widget(['template' => $model]) ?>
    </div>
</div>

<script type="text/javascript">
    var $sourceInput = $('#template-form-source');
    $sourceInput.autosize();

    $sourceInput.on('change', function () {
        $sourceInput.data('changed', true);
    });

    $('#sourceForm').on('submit', function () {
        $sourceInput.data('changed', false);
    });

    // Note some browser do not support custom messages for this event.
    $(window).on('beforeunload', function (evt) {
        if ($sourceInput.data('changed')) {
            return "<?= Yii::t('CustomPagesModule.modules_template_views_admin_editSource', "You haven't saved your last changes yet. Do you want to leave without saving?") ?>";
        }
    });

    $(document).on('keydown', '#template-form-source', function (e) {
        var keyCode = e.keyCode || e.which;

        if (keyCode === 9) {
            e.preventDefault();

            $this = $(this);
            var start = $this.get(0).selectionStart;
            var end = $this.get(0).selectionEnd;

            // set textarea value to: text before caret + tab + text after caret
            $(this).val($(this).val().substring(0, start)
                    + "\t"
                    + $this.val().substring(end));

            // put caret at right position again
            $this.get(0).selectionStart = $this.get(0).selectionEnd = start + 1;
        }
    });

    $('#addElementSelect a').on('click', function (evt) {
        evt.preventDefault();
        var type = $(this).data('content-type');
        $.ajax('<?= Url::to(['/custom_pages/template/admin/add-element']) ?>', {
            dataType: 'json',
            data: {
                template_id: <?= $model->id ?>,
                content_type: type
            },
            beforeSend: function () {
                setModalLoader();
                $('#globalModal').modal('show');
            },
            success: function (json) {
                $('#globalModal').html(json.content);
            },
            error: function () {

            }
        });
    });

    $('#editAllElements').on('click', function (evt) {
        $.ajax('<?= Url::to(['/custom_pages/template/admin/edit-multiple']) ?>', {
            dataType: 'json',
            data: {
                id: <?= $model->id ?>,
            },
            beforeSend: function () {
                setModalLoader();
                $('#globalModal').modal('show');
            },
            success: function (json) {
                $('#globalModal').html(json.content);
            },
        });
    });

    $(document).on('templateMultipleElementEditSuccess', function (evt, json) {
        $('#globalModal').modal('hide');
        if (json.content) {
            $('#templateElementTable').replaceWith(json.content);
        }
    });
    
    $(document).on('templateElementDeleteSuccess', function (evt, json) {
        $('#globalModal').modal('hide');
        $('[data-template-element-definition="' + json.id + '"]').fadeOut('fast', function() {
            $(this).remove();
        });
    });

    $(document).on('templateElementEditSuccess', function (evt, json) {
        $('#globalModal').modal('hide');
        var $currentRow = $('[data-template-element-definition="' + json.id + '"]');
        if (!$currentRow.length) {
            var $content = $(json.content).hide();
            $('#templateElements').append($content);
            $content.fadeIn('fast');

            if (json.name) {
                insertPlaceholder('{{ ' + json.name + ' }}');
            }
        } else {
            $currentRow.replaceWith(json.content);
        }
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

            var re = el.createTextRange();
            var rc = re.duplicate();
            re.moveToBookmark(r.getBookmark());
            rc.setEndPoint('EndToStart', re);

            return rc.text.length;
        }
        return 0;
    }

    function insertPlaceholder(txt) {
        var textarea = document.getElementById('template-form-source');
        var currentPos = getCaret(textarea);
        var strLeft = textarea.value.substring(0, currentPos);
        var strRight = textarea.value.substring(currentPos, textarea.value.length);
        textarea.value = strLeft + txt + strRight;
        $(textarea).trigger('change');
    }
</script>