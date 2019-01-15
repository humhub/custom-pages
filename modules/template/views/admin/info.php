<?php

use yii\widgets\ActiveForm;

/* @var $model humhub\modules\custom_pages\modules\template\models\forms\AddCotnainerItemForm */

?>
<div class="modal-dialog modal-dialog-large">
    <div class="modal-content media">
        <?php $form = ActiveForm::begin(['enableClientValidation' => false]);?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">
                   <?= Yii::t('CustomPagesModule.modules_template_views_admin_info', '<strong>Template</strong> Infos'); ?>
                </h4>
            </div>
            <div class="modal-body media-body">  
                <div class="info-content">
                    <?= Yii::t('CustomPagesModule.modules_template_views_admin_info', ''
                            . 'This template systems uses {twigLink} as template engine.<br /><br /> '
                            . 'You can add elements as Richtexts or Images into your template by using the \'Add Element\' dropdown menu.<br />'
                            . 'After adding an element, the elements placeholder is automatically inserted to your template.<br />'
                            . 'You can change the position of your elements anytime. The element for an block with the name \'content\' '
                            . 'will be represented as {contentVar} within your template.<br /><br /> The name of your block hast to start with an '
                            . 'alphanumeric letter and cannot contain any special signs except an \'_\'. '
                            . 'Each element provides additional placeholder for rendering the default content or edit links. '
                            . 'These additions can be inserted adding for example {contentDefaultVar} to your template.', [
                                    'twigLink' => '<strong><a href="http://twig.sensiolabs.org/">Twig</a></strong>',
                        'contentVar' => '{{ content }}',
                        'contentDefaultVar' => '{{ content.default }}'
                        ]); ?>
                    <br /><br />
                    <?= Yii::t('CustomPagesModule.modules_template_views_admin_info', 'More infos about the twig syntax is available <strong><a href="{twig_tmpl_url}">here</a></strong>', ['twig_tmpl_url' => 'http://twig.sensiolabs.org/doc/templates.html']); ?>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" data-dismiss="modal"><?= Yii::t('CustomPagesModule.base', 'Close'); ?></button>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>