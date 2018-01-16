<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

/* @var $this \humhub\components\View */
?>

<div id="test-page">
    My name is: <?= Yii::$app->user->getIdentity()->getDisplayName()?>
</div>