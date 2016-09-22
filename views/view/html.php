<?php
$cssClass = ($page->hasAttribute('cssClass') && !empty($page->cssClass)) ? $page->cssClass : 'custom-pages-page';
?>

<div class="container <?= $cssClass ?>">
    <div class="row">

        <div class="col-md-12">

            <?php echo $html; ?>

        </div>
    </div>
</div>
