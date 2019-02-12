<?php

namespace humhub\modules\custom_pages\interfaces;

use humhub\modules\content\components\ActiveQueryContent;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\custom_pages\models\ContainerPage;
use humhub\modules\custom_pages\models\Page;
use humhub\modules\custom_pages\models\PageType;
use humhub\modules\custom_pages\models\Target;
use yii\base\Component;
use yii\db\ActiveQuery;

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
                    foreach (Page::getNavigationClasses() as $targetId => $name) {
                        $event->addTarget(['id' => $targetId, 'name' => $name]);
                    }
                }
                break;
        }
    }

    /**
     * @param $targetId
     * @param ContentContainerActiveRecord|null $container
     * @return mixed|null
     */
    public function getTargetById($targetId, ContentContainerActiveRecord $container = null)
    {
        $availableNavs = $this->getTargets($container);
        return array_key_exists($targetId, $availableNavs) ? $availableNavs[$targetId] : null;
    }

    /**
     * Returns all pages related to a given target.
     *
     * @param string|Target $targetId
     * @param ContentContainerActiveRecord|null $container
     * @return ActiveQuery
     * @throws \yii\base\Exception
     */
    public function findPagesByTarget($targetId, ContentContainerActiveRecord $container = null)
    {
        if ($targetId instanceof Target) {
            $container = $targetId->container;
            $targetId = $targetId->id;
        }


        $query = $container ? ContainerPage::find() : Page::find();
        $query->where(['navigation_class' => $targetId]);

        if ($query instanceof ActiveQueryContent && $container) {
            $query->readable()->contentContainer($container);
        }

        return $query->orderBy('sort_order');
    }

}