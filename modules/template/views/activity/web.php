<?php

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\space\models\Space;
use humhub\modules\space\widgets\Image;
use humhub\widgets\TimeAgo;
use yii\helpers\Url;

/**
 * Server-side HTML of one activity entry, used by `ActivityElementVariable` for the template variable `activity.html`.
 *
 * Why this file exists: until HumHub 1.20 the core rendered this markup itself (`RenderService::getWeb()` with the
 * view `activity/views/layouts/web.php`). Since 1.20 the activity box is a Vue island and the entry markup is built
 * in the browser (`ActivityEntry.vue`), so the server-side renderer was removed without a replacement. This is a copy
 * of the removed core view, kept only to leave the output of `activity.html` in the templates unchanged.
 *
 * TODO: Find a better way and delete this file. The copy does not follow changes of the core's entry markup. Options:
 * output only `activity.message` (`BaseActivity::asWeb()`) without the wrapper, or ask the core for a public helper.
 */

/* @var $user \humhub\modules\user\models\User */
/* @var $url string|null */
/* @var $contentContainer \humhub\modules\content\models\ContentContainer|null */
/* @var $createdAt string */
/* @var $message string */

?>

<?php if (!empty($url)) : ?>
<a href="<?= Url::to($url) ?>">
<?php endif; ?>

    <div class="d-flex activity-box-entry">
        <div class="flex-shrink-0 me-3 pt-1 img-profile-space">
            <?= $user->getProfileImage()->render(32, ['link' => false]) ?>

            <?php if (!Yii::$app->controller instanceof ContentContainerController && $contentContainer?->polymorphicRelation instanceof Space) : ?>
                <?= Image::widget([
                    'space' => $contentContainer->polymorphicRelation,
                    'width' => 20,
                    'htmlOptions' => ['class' => 'img-space'],
                ]) ?>
            <?php endif; ?>
        </div>

        <div class="flex-grow-1 text-break">
            <?= $message ?>
            <br>
            <?= TimeAgo::widget(['timestamp' => $createdAt]) ?>
        </div>
    </div>

<?php if (!empty($url)) : ?>
</a>
<?php endif; ?>
