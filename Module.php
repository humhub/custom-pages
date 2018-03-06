<?php

namespace humhub\modules\custom_pages;

use Yii;
use yii\helpers\Url;
use humhub\modules\custom_pages\models\Page;
use humhub\modules\custom_pages\models\ContainerPage;
use humhub\modules\space\models\Space;
use humhub\modules\content\components\ContentContainerActiveRecord;

class Module extends \humhub\modules\content\components\ContentContainerModule
{

    public $resourcesPath = 'resources';
    
    public function init()
    {
        self::loadTwig();
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function getConfigUrl()
    {
        return Url::to(['/custom_pages/admin/settings']);
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
        
        foreach (models\Snippet::find()->all() as $page) {
            $page->delete();
        }
        
        foreach (models\ContainerSnippet::find()->all() as $page) {
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

    /**
     * @inheritdoc
     */
    public function getContentContainerName(ContentContainerActiveRecord $container)
    {
        return Yii::t('CustomPagesModule.base', 'Custom pages');
    }

    public function getContentContainerDescription(ContentContainerActiveRecord $container)
    {
        if ($container instanceof Space) {
            return Yii::t('CustomPagesModule.base', 'Allows to add pages (markdown, iframe or links) to the space navigation');
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
        
        foreach (models\ContainerSnippet::find()->contentContainer($container)->all() as $page) {
            $page->delete();
        }
    }

    public static function loadTwig()
    {
        $autoloader = Yii::getAlias('@custom_pages/vendors/Twig/Autoloader.php');
        require_once $autoloader;
        \Twig_Autoloader::register();
    }

}
