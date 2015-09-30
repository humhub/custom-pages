<?php

namespace humhub\modules\custom_pages;

use yii\helpers\Url;
use humhub\modules\custom_pages\models\Page;
use humhub\modules\custom_pages\models\ContainerPage;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use humhub\modules\content\components\ContentContainerActiveRecord;

class Module extends \humhub\modules\content\components\ContentContainerModule
{

    /**
     * @inheritdoc
     */
    public function getConfigUrl()
    {
        return Url::to(['/custom_pages/admin']);
    }

    /**
     * @inheritdoc
     */
    public function disable()
    {
        foreach (Page::find()->all() as $page) {
            $page->delete();
        }

        foreach (ContainerPage::find()->all() as $page) {
            $page->delete();
        }

        parent::disable();
    }

    /**
     * @inheritdoc
     */
    public function getContentContainerTypes()
    {
        return [
            Space::className(),
        ];
    }

    public function getContentContainerDescription(ContentContainerActiveRecord $container)
    {
        if ($container instanceof Space) {
            return "Allows to add pages (markdown, iframe or links) to the space navigation";
        }
    }

    /**
     * @inheritdoc
     */
    public function disableContentContainer(ContentContainerActiveRecord $container)
    {
        parent::disableContentContainer($container);

        foreach (ContainerPage::find()->contentContainer($container)->all() as $page) {
            $page->delete();
        }
    }

}
