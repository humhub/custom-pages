<?php

namespace humhub\modules\custom_pages\interfaces;

use humhub\modules\content\components\ActiveQueryContent;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\custom_pages\models\ContainerPage;
use humhub\modules\custom_pages\models\ContainerSnippet;
use humhub\modules\custom_pages\models\CustomContentContainer;
use humhub\modules\custom_pages\models\Page;
use humhub\modules\custom_pages\models\PageType;
use humhub\modules\custom_pages\models\Snippet;
use humhub\modules\custom_pages\models\Target;
use humhub\modules\space\models\Space;
use yii\base\Component;
use yii\base\InvalidArgumentException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Class CustomPagesService
 * @package humhub\modules\custom_pages\interfaces
 */
class CustomPagesService extends Component
{

    const EVENT_FETCH_TARGETS = 'fetchTargets';

    private static $targetCache = [];

    /**
     * Fetches all available navigations for a given container.
     *
     * @param string $type
     * @param ContentContainerActiveRecord $container
     * @return array
     */
    public function getTargets($type, ContentContainerActiveRecord $container = null)
    {
        $containerKey = ($container) ? $container->contentcontainer_id : 'global';

        if (isset(static::$targetCache[$containerKey])) {
            return static::$targetCache[$containerKey];
        }

        $event = new CustomPagesTargetEvent(['type' => $type, 'container' => $container]);
        $this->addDefaultTargets($event);

        $this->trigger(self::EVENT_FETCH_TARGETS, $event);

        return static::$targetCache[$containerKey] = $event->getTargets();
    }

    public function addDefaultTargets(CustomPagesTargetEvent $event)
    {
        switch ($event->type) {
            case PageType::Page:
                if (!$event->container) {
                    $event->addTargets(Page::getDefaultTargets());
                } else if($event->container instanceof Space) {
                    $event->addTargets(ContainerPage::getDefaultTargets());
                }
                break;
            case PageType::Snippet:
                if(!$event->container) {
                    foreach (Snippet::getSidebarSelection() as $targetId => $name) {
                        $event->addTarget(['id' => $targetId, 'name' => $name]);
                    }
                } else {
                    foreach (ContainerSnippet::getSidebarSelection() as $targetId => $name) {
                        $event->addTarget(['id' => $targetId, 'name' => $name]);
                    }
                }
        }
    }

    /**
     * @param string $targetId
     * @param string $type
     * @param ContentContainerActiveRecord|null $container
     * @return CustomContentContainer|null
     */
    public function getTargetById($targetId, $type, ContentContainerActiveRecord $container = null)
    {
        $availableTargets = $this->getTargets($type, $container);
        return array_key_exists($targetId, $availableTargets) ? $availableTargets[$targetId] : null;
    }

    /**
     * Returns all pages related to a given target.
     *
     * @param string|Target $targetId
     * @param ContentContainerActiveRecord|null $container
     * @return ActiveQueryContent
     * @throws \yii\base\Exception
     */
    public function findContentByTarget($targetId, $type, ContentContainerActiveRecord $container = null)
    {
        if ($targetId instanceof Target) {
            $container = $targetId->container;
            $targetId = $targetId->id;
        }


        $contentClass = $this->getContentClass($type, $container);

        $query = call_user_func($contentClass.'::find');

        $query->where(['target' => $targetId])->contentContainer($container);

        return $query->orderBy('sort_order')->orderBy('id DESC');
    }

    /**
     * @param $type
     * @param ContentContainerActiveRecord|null $container
     * @return string
     */
    private function getContentClass($type, ContentContainerActiveRecord $container = null)
    {
        if(PageType::Page === $type) {
            return ($container) ?  ContainerPage::class : Page::class;
        } else if(PageType::Snippet === $type) {
           return ($container) ?  ContainerSnippet::class : Snippet::class;
        } else {
            throw new InvalidArgumentException('Invalid page type selection in findContentByTarget()');
        }
    }

    /**
     * Should be called to search for a single custom content with a given id.
     *
     * @param $id
     * @param $targetId
     * @param $type
     * @param ContentContainerActiveRecord|null $container
     * @return CustomContentContainer
     * @throws \yii\base\Exception
     */
    public function getSingleContent($id, $targetId, $type, ContentContainerActiveRecord $container = null)
    {
        $contentClass = $this->getContentClass($type, $container);
        $tableName = call_user_func($contentClass.'::tableName');
        return $this->findContentByTarget($targetId, $type, $container)->where([$tableName.'.id' => $id])->one();
    }

}