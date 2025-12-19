<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\custom_pages\assets\Assets;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\modules\template\widgets\PageConfigurationButton;
use humhub\modules\ui\view\components\View;
use yii\helpers\Html;

/* @var $this View */
/* @var $page CustomPage */
/* @var $html string */

$cssClass = ($page->hasAttribute('cssClass') && !empty($page->cssClass)) ? $page->cssClass : 'custom-pages-page';

Assets::register($this);
?>
<?= PageConfigurationButton::widget() ?>
<div class="panel panel-default <?= Html::encode($cssClass) ?>">
    <div class="panel-body">
        <?= $html ?>
    </div>
</div>
