<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $allowedTemplateSelection array */
/* @var $action string */

?>
<?php humhub\widgets\ModalDialog::begin(['header' => Yii::t('CustomPagesModule.modules_template_views_element_addItemChooseTemplate', 'Choose a template'), 'size' => 'large']) ?>
    <?php $form = ActiveForm::begin(['action' => $action, 'enableClientValidation' => false]);?>
        <div class="modal-body media-body">  
            <div class="form-group field-templateelement-name required">
                <label class="control-label" for="templateSelection"><?= Yii::t('CustomPagesModule.modules_template_views_element_addItemChooseTemplate', 'Template'); ?></label>
                <?= Html::dropDownList('templateId', null, $allowedTemplateSelection, ['id' => 'templateSelection', 'class' => 'form-control', 'data-ui-select2' => '1']) ?>
            </div>
        </div>
        <div class="modal-footer">
            <button data-action-click="ui.modal.submit" data-action-data-type="json" type="submit" data-ui-loader class="btn btn-primary">
                        <?= Yii::t('CustomPagesModule.base', 'Submit'); ?>
            </button>
            <button type="button" class="btn btn-primary" data-dismiss="modal"><?= Yii::t('CustomPagesModule.base', 'Cancel'); ?></button>
        </div>
    <?php ActiveForm::end(); ?>
<?php humhub\widgets\ModalDialog::end() ?>