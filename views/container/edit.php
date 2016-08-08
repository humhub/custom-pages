<?php

use yii\helpers\Html;
use humhub\compat\CActiveForm;
use humhub\modules\custom_pages\models\ContainerPage;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\Assets;

Assets::register($this);
?>
<div class="panel panel-default">
    <?php if (!$page->isNewRecord) : ?>
        <div class="panel-heading"><?php echo Yii::t('CustomPagesModule.views_admin_edit', '<strong>Edit</strong> page'); ?></div>
    <?php else: ?>
        <div class="panel-heading"><?php echo Yii::t('CustomPagesModule.views_admin_edit', '<strong>Create</strong> page'); ?></div>
    <?php endif; ?>
    <div class="panel-body">

        <?php $form = CActiveForm::begin(); ?>

        <?php echo $form->errorSummary($page); ?>

        <div class="form-group">
            <?php echo $form->labelEx($page, 'type'); ?><br />
            <?php $types = $page->getPageTypes(); ?>
            <?php echo $types[$page->type]; ?>
        </div>

        <div class="form-group">
            <?php echo $form->labelEx($page, 'title'); ?>
            <?php echo $form->textField($page, 'title', array('class' => 'form-control', 'placeholder' => Yii::t('CustomPagesModule.views_admin_edit', 'Page title'))); ?>
        </div>

        <?php if ($page->type == ContainerPage::TYPE_TEMPLATE): ?>
            <div class="form-group" id="content_field">
                <?php echo $form->labelEx($page, 'templateId'); ?>
                <?php echo $form->dropdownList($page, 'templateId', Template::getSelection(['type' => Template::TYPE_LAYOUT, 'allow_for_spaces' => '1']), array('class' => 'form-control', 'rows' => '15', 'placeholder' => Yii::t('CustomPagesModule.views_admin_edit', 'Template'))); ?>
            </div>
        <?php elseif ($page->type == ContainerPage::TYPE_MARKDOWN): ?>
            <?php echo $form->textArea($page, 'page_content', array('id' => 'markdownField', 'class' => 'form-control', 'rows' => '15', 'placeholder' => Yii::t('CustomPagesModule.views_admin_edit', 'Content'))); ?>
            <?php echo \humhub\widgets\MarkdownEditor::widget(['fieldId' => 'markdownField']); ?>
        <?php elseif ($page->type == ContainerPage::TYPE_LINK || $page->type == ContainerPage::TYPE_IFRAME): ?>
            <div class="form-group">
                <?php echo $form->labelEx($page, 'url'); ?>
                <?php echo $form->textField($page, 'url', array('class' => 'form-control', 'placeholder' => Yii::t('CustomPagesModule.views_admin_edit', 'URL'))); ?>
            </div>
        <?php endif; ?>
        <div class="form-group">
            <?php echo $form->labelEx($page, 'sort_order'); ?>
            <?php echo $form->textField($page, 'sort_order', array('class' => 'form-control', 'placeholder' => Yii::t('CustomPagesModule.views_admin_edit', 'Sort Order'))); ?>
            <p class="help-block"><?php echo Yii::t('CustomPagesModule.views_admin_edit', 'Default sort orders scheme: 100, 200, 300, ...'); ?></p>
        </div>

        <?php
        $faIcons = array("fa-adjust", "fa-adn", "fa-align-center", "fa-align-justify", "fa-align-left", "fa-align-right", "fa-ambulance", "fa-anchor", "fa-android", "fa-angellist", "fa-angle-double-down", "fa-angle-double-left", "fa-angle-double-right", "fa-angle-double-up", "fa-angle-down", "fa-angle-left", "fa-angle-right", "fa-angle-up", "fa-apple", "fa-archive", "fa-area-chart", "fa-arrow-circle-down", "fa-arrow-circle-left", "fa-arrow-circle-o-down", "fa-arrow-circle-o-left", "fa-arrow-circle-o-right", "fa-arrow-circle-o-up", "fa-arrow-circle-right", "fa-arrow-circle-up", "fa-arrow-down", "fa-arrow-left", "fa-arrow-right", "fa-arrow-up", "fa-arrows", "fa-arrows-alt", "fa-arrows-h", "fa-arrows-v", "fa-asterisk", "fa-at", "fa-automobile", "fa-backward", "fa-ban", "fa-bank", "fa-bar-chart", "fa-bar-chart-o", "fa-barcode", "fa-bars", "fa-beer", "fa-behance", "fa-behance-square", "fa-bell", "fa-bell-o", "fa-bell-slash", "fa-bell-slash-o", "fa-bicycle", "fa-binoculars", "fa-birthday-cake", "fa-bitbucket", "fa-bitbucket-square", "fa-bitcoin", "fa-bold", "fa-bolt", "fa-bomb", "fa-book", "fa-bookmark", "fa-bookmark-o", "fa-briefcase", "fa-btc", "fa-bug", "fa-building", "fa-building-o", "fa-bullhorn", "fa-bullseye", "fa-bus", "fa-cab", "fa-calculator", "fa-calendar", "fa-calendar-o", "fa-camera", "fa-camera-retro", "fa-car", "fa-caret-down", "fa-caret-left", "fa-caret-right", "fa-caret-square-o-down", "fa-caret-square-o-left", "fa-caret-square-o-right", "fa-caret-square-o-up", "fa-caret-up", "fa-cc", "fa-cc-amex", "fa-cc-discover", "fa-cc-mastercard", "fa-cc-paypal", "fa-cc-stripe", "fa-cc-visa", "fa-certificate", "fa-chain", "fa-chain-broken", "fa-check", "fa-check-circle", "fa-check-circle-o", "fa-check-square", "fa-check-square-o", "fa-chevron-circle-down", "fa-chevron-circle-left", "fa-chevron-circle-right", "fa-chevron-circle-up", "fa-chevron-down", "fa-chevron-left", "fa-chevron-right", "fa-chevron-up", "fa-child", "fa-circle", "fa-circle-o", "fa-circle-o-notch", "fa-circle-thin", "fa-clipboard", "fa-clock-o", "fa-close", "fa-cloud", "fa-cloud-download", "fa-cloud-upload", "fa-cny", "fa-code", "fa-code-fork", "fa-codepen", "fa-coffee", "fa-cog", "fa-cogs", "fa-columns", "fa-comment", "fa-comment-o", "fa-comments", "fa-comments-o", "fa-compass", "fa-compress", "fa-copy", "fa-copyright", "fa-credit-card", "fa-crop", "fa-crosshairs", "fa-css3", "fa-cube", "fa-cubes", "fa-cut", "fa-cutlery", "fa-dashboard", "fa-database", "fa-dedent", "fa-delicious", "fa-desktop", "fa-deviantart", "fa-digg", "fa-dollar", "fa-dot-circle-o", "fa-download", "fa-dribbble", "fa-dropbox", "fa-drupal", "fa-edit", "fa-eject", "fa-ellipsis-h", "fa-ellipsis-v", "fa-empire", "fa-envelope", "fa-envelope-o", "fa-envelope-square", "fa-eraser", "fa-eur", "fa-euro", "fa-exchange", "fa-exclamation", "fa-exclamation-circle", "fa-exclamation-triangle", "fa-expand", "fa-external-link", "fa-external-link-square", "fa-eye", "fa-eye-slash", "fa-eyedropper", "fa-facebook", "fa-facebook-square", "fa-fast-backward", "fa-fast-forward", "fa-fax", "fa-female", "fa-fighter-jet", "fa-file", "fa-file-archive-o", "fa-file-audio-o", "fa-file-code-o", "fa-file-excel-o", "fa-file-image-o", "fa-file-movie-o", "fa-file-o", "fa-file-pdf-o", "fa-file-photo-o", "fa-file-picture-o", "fa-file-powerpoint-o", "fa-file-sound-o", "fa-file-text", "fa-file-text-o", "fa-file-video-o", "fa-file-word-o", "fa-file-zip-o", "fa-files-o", "fa-film", "fa-filter", "fa-fire", "fa-fire-extinguisher", "fa-flag", "fa-flag-checkered", "fa-flag-o", "fa-flash", "fa-flask", "fa-flickr", "fa-floppy-o", "fa-folder", "fa-folder-o", "fa-folder-open", "fa-folder-open-o", "fa-font", "fa-forward", "fa-foursquare", "fa-frown-o", "fa-futbol-o", "fa-gamepad", "fa-gavel", "fa-gbp", "fa-ge", "fa-gear", "fa-gears", "fa-gift", "fa-git", "fa-git-square", "fa-github", "fa-github-alt", "fa-github-square", "fa-gittip", "fa-glass", "fa-globe", "fa-google", "fa-google-plus", "fa-google-plus-square", "fa-google-wallet", "fa-graduation-cap", "fa-group", "fa-h-square", "fa-hacker-news", "fa-hand-o-down", "fa-hand-o-left", "fa-hand-o-right", "fa-hand-o-up", "fa-hdd-o", "fa-header", "fa-headphones", "fa-heart", "fa-heart-o", "fa-history", "fa-home", "fa-hospital-o", "fa-html5", "fa-ils", "fa-image", "fa-inbox", "fa-indent", "fa-info", "fa-info-circle", "fa-inr", "fa-instagram", "fa-institution", "fa-ioxhost", "fa-italic", "fa-joomla", "fa-jpy", "fa-jsfiddle", "fa-key", "fa-keyboard-o", "fa-krw", "fa-language", "fa-laptop", "fa-lastfm", "fa-lastfm-square", "fa-leaf", "fa-legal", "fa-lemon-o", "fa-level-down", "fa-level-up", "fa-life-bouy", "fa-life-buoy", "fa-life-ring", "fa-life-saver", "fa-lightbulb-o", "fa-line-chart", "fa-link", "fa-linkedin", "fa-linkedin-square", "fa-linux", "fa-list", "fa-list-alt", "fa-list-ol", "fa-list-ul", "fa-location-arrow", "fa-lock", "fa-long-arrow-down", "fa-long-arrow-left", "fa-long-arrow-right", "fa-long-arrow-up", "fa-magic", "fa-magnet", "fa-mail-forward", "fa-mail-reply", "fa-mail-reply-all", "fa-male", "fa-map-marker", "fa-maxcdn", "fa-meanpath", "fa-medkit", "fa-meh-o", "fa-microphone", "fa-microphone-slash", "fa-minus", "fa-minus-circle", "fa-minus-square", "fa-minus-square-o", "fa-mobile", "fa-mobile-phone", "fa-money", "fa-moon-o", "fa-mortar-board", "fa-music", "fa-navicon", "fa-newspaper-o", "fa-openid", "fa-outdent", "fa-pagelines", "fa-paint-brush", "fa-paper-plane", "fa-paper-plane-o", "fa-paperclip", "fa-paragraph", "fa-paste", "fa-pause", "fa-paw", "fa-paypal", "fa-pencil", "fa-pencil-square", "fa-pencil-square-o", "fa-phone", "fa-phone-square", "fa-photo", "fa-picture-o", "fa-pie-chart", "fa-pied-piper", "fa-pied-piper-alt", "fa-pinterest", "fa-pinterest-square", "fa-plane", "fa-play", "fa-play-circle", "fa-play-circle-o", "fa-plug", "fa-plus", "fa-plus-circle", "fa-plus-square", "fa-plus-square-o", "fa-power-off", "fa-print", "fa-puzzle-piece", "fa-qq", "fa-qrcode", "fa-question", "fa-question-circle", "fa-quote-left", "fa-quote-right", "fa-ra", "fa-random", "fa-rebel", "fa-recycle", "fa-reddit", "fa-reddit-square", "fa-refresh", "fa-remove", "fa-renren", "fa-reorder", "fa-repeat", "fa-reply", "fa-reply-all", "fa-retweet", "fa-rmb", "fa-road", "fa-rocket", "fa-rotate-left", "fa-rotate-right", "fa-rouble", "fa-rss", "fa-rss-square", "fa-rub", "fa-ruble", "fa-rupee", "fa-save", "fa-scissors", "fa-search", "fa-search-minus", "fa-search-plus", "fa-send", "fa-send-o", "fa-share", "fa-share-alt", "fa-share-alt-square", "fa-share-square", "fa-share-square-o", "fa-shekel", "fa-sheqel", "fa-shield", "fa-shopping-cart", "fa-sign-in", "fa-sign-out", "fa-signal", "fa-sitemap", "fa-skype", "fa-slack", "fa-sliders", "fa-slideshare", "fa-smile-o", "fa-soccer-ball-o", "fa-sort", "fa-sort-alpha-asc", "fa-sort-alpha-desc", "fa-sort-amount-asc", "fa-sort-amount-desc", "fa-sort-asc", "fa-sort-desc", "fa-sort-down", "fa-sort-numeric-asc", "fa-sort-numeric-desc", "fa-sort-up", "fa-soundcloud", "fa-space-shuttle", "fa-spinner", "fa-spoon", "fa-spotify", "fa-square", "fa-square-o", "fa-stack-exchange", "fa-stack-overflow", "fa-star", "fa-star-half", "fa-star-half-empty", "fa-star-half-full", "fa-star-half-o", "fa-star-o", "fa-steam", "fa-steam-square", "fa-step-backward", "fa-step-forward", "fa-stethoscope", "fa-stop", "fa-strikethrough", "fa-stumbleupon", "fa-stumbleupon-circle", "fa-subscript", "fa-suitcase", "fa-sun-o", "fa-superscript", "fa-support", "fa-table", "fa-tablet", "fa-tachometer", "fa-tag", "fa-tags", "fa-tasks", "fa-taxi", "fa-tencent-weibo", "fa-terminal", "fa-text-height", "fa-text-width", "fa-th", "fa-th-large", "fa-th-list", "fa-thumb-tack", "fa-thumbs-down", "fa-thumbs-o-down", "fa-thumbs-o-up", "fa-thumbs-up", "fa-ticket", "fa-times", "fa-times-circle", "fa-times-circle-o", "fa-tint", "fa-toggle-down", "fa-toggle-left", "fa-toggle-off", "fa-toggle-on", "fa-toggle-right", "fa-toggle-up", "fa-trash", "fa-trash-o", "fa-tree", "fa-trello", "fa-trophy", "fa-truck", "fa-try", "fa-tty", "fa-tumblr", "fa-tumblr-square", "fa-turkish-lira", "fa-twitch", "fa-twitter", "fa-twitter-square", "fa-umbrella", "fa-underline", "fa-undo", "fa-university", "fa-unlink", "fa-unlock", "fa-unlock-alt", "fa-unsorted", "fa-upload", "fa-usd", "fa-user", "fa-user-md", "fa-users", "fa-video-camera", "fa-vimeo-square", "fa-vine", "fa-vk", "fa-volume-down", "fa-volume-off", "fa-volume-up", "fa-warning", "fa-wechat", "fa-weibo", "fa-weixin", "fa-wheelchair", "fa-wifi", "fa-windows", "fa-won", "fa-wordpress", "fa-wrench", "fa-xing", "fa-xing-square", "fa-yahoo", "fa-yelp", "fa-yen", "fa-youtube", "fa-youtube-play");
        ?>

        <div class="form-group">
            <?php echo $form->labelEx($page, 'icon'); ?>

            <select class='selectpicker form-control' name="ContainerPage[icon]">
                <?php foreach ($faIcons as $icon): ?>
                    <option data-content="<i class='fa  <?php echo $icon; ?>'></i>" value="<?php echo $icon; ?>" <?php if ($page->icon == $icon): ?>selected='selected'<?php endif; ?>><?php echo $icon; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <div class="checkbox">
                <label>
                    <?php echo $form->checkBox($page, 'in_new_window'); ?> <?php echo $page->getAttributeLabel('in_new_window'); ?>
                </label>
            </div>
        </div>

        <?php echo Html::submitButton(Yii::t('CustomPagesModule.views_admin_edit', 'Save'), ['class' => 'btn btn-primary', 'data-ui-loader' => '']); ?>

        <?php
        if (!$page->isNewRecord) {
            echo Html::a(Yii::t('CustomPagesModule.views_admin_edit', 'Delete'), $page->content->container->createUrl('delete', ['id' => $page->id]), array('class' => 'btn btn-danger', 'data-confirm' => 'Are you sure?'));
        }
        ?>

        <?php CActiveForm::end(); ?>

    </div>
</div>
