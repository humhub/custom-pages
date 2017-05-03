<?php

use humhub\modules\directory\widgets\Menu;

\humhub\assets\JqueryKnobAsset::register($this);
?>

<div class="container">
    <div class="row">
        <div class="col-md-2">
            <?= Menu::widget(); ?>
        </div>
        <div class="col-md-10">
            <?= $content; ?>
        </div>
    </div>
</div>
