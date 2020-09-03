<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

/* @var $this \humhub\modules\ui\view\components\View */
?>

<div id="test-snippet">
    My name is: <?= Yii::$app->user->getIdentity()->getDisplayName()?>
</div>