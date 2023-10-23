<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\widgets\Button;

/* @var string $url */
/* @var array $options */
?>
<?= Button::danger(Yii::t('CustomPagesModule.base', 'Delete'))
    ->action('custom_pages.template.deleteElementContent', $url)
    ->icon('times')
    ->tooltip(Yii::t('CustomPagesModule.base', 'Reset the content to default value'))
    ->options($options)
    ->confirm(Yii::t('CustomPagesModule.modules_template_controller_OwnerContentController', '<strong>Confirm</strong> content deletion'),
        Yii::t('CustomPagesModule.modules_template_widgets_views_confirmDeletionModal', 'Do you really want to delete this content?'),
        Yii::t('CustomPagesModule.base', 'Delete'))
?>
