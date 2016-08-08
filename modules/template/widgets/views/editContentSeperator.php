<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

?>

<?php if($isAdminEdit) : ?>
    <hr class="hr-text" data-content="<?= Yii::t('CustomPagesModule.base', 'Default Content'); ?>">
<?php else: ?>
    <hr class="hr-text" data-content="<?= Yii::t('CustomPagesModule.base', 'Content'); ?>">
<?php endif; ?>