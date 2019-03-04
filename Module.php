<?php

namespace humhub\modules\custom_pages;

use Yii;
use humhub\modules\custom_pages\models\Snippet;
use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\custom_pages\models\Page;
use humhub\modules\custom_pages\models\ContainerPage;
use humhub\modules\space\models\Space;
use humhub\modules\content\components\ContentContainerActiveRecord;

class Module extends \humhub\modules\content\components\ContentContainerModule
{
    const ICON = 'fa-file-text-o';
    const SETTING_MIGRATION_KEY = 'global_pages_migrated';

    public $resourcesPath = 'resources';
    
    public function init()
    {
        $this->checkOldGlobalContent();
        self::loadTwig();
        parent::init();
    }

    public function checkOldGlobalContent()
    {
        if(!$this->settings->get(static::SETTING_MIGRATION_KEY, 0)) {
            foreach (Page::find()->all() as $page) {
                $page->content->save();
            }

            foreach (Snippet::find()->all() as $page) {
                $page->content->save();
            }

            $this->settings->set(static::SETTING_MIGRATION_KEY, 1);
        }
    }

    /**
     * @inheritdoc
     */
    public function getConfigUrl()
    {
        return Url::toModuleConfig();
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
            Space::class,
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
