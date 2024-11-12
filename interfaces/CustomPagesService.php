<?php

namespace humhub\modules\custom_pages\interfaces;

use humhub\modules\content\components\ActiveQueryContent;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\custom_pages\models\CustomContentContainer;
use humhub\modules\custom_pages\models\Page;
use humhub\modules\custom_pages\models\PageType;
use humhub\modules\custom_pages\models\Target;
use yii\base\Component;

/**
 * Class CustomPagesService
 * @package humhub\modules\custom_pages\interfaces
 */
class CustomPagesService extends Component
{
    public const EVENT_FETCH_TARGETS = 'fetchTargets';

    /**
     * Fetches all available navigations for a given container.
     *
     * @param string $type
     * @param ?ContentContainerActiveRecord $container
     * @return array
     */
    public function getTargets(string $type, ?ContentContainerActiveRecord $container = null): array
    {
        static $cache;

        $containerKey = $container ? $container->contentcontainer_id : 'global';

        if (!isset($cache[$type][$containerKey])) {
            $event = new CustomPagesTargetEvent(['type' => $type, 'container' => $container]);
            $event->addDefaultTargets();

            $this->trigger(self::EVENT_FETCH_TARGETS, $event);

            $cache[$type][$containerKey] = $event->getTargets();
        }

        return $cache[$type][$containerKey];
    }

    /**
     * @param string $targetId
     * @param string $type
     * @param ContentContainerActiveRecord|null $container
     * @return Target
     */
    public function getTargetById($targetId, $type, ContentContainerActiveRecord $container = null)
    {
        $availableTargets = $this->getTargets($type, $container);
        return array_key_exists($targetId, $availableTargets) ? $availableTargets[$targetId] : null;
    }

    public function getTargetByPage(CustomContentContainer $page): ?Target
    {
        $types = [PageType::Page, PageType::Snippet];

        foreach ($types as $type) {
            if ($target = $this->getTargetById($page->target, $type, $page->content->container)) {
                return $target;
            }
        }

        return null;
    }

    /**
     * Deletes all target pages of the given type and container.
     *
     * Note: This will only delete global pages if no container is given, use [[deleteAllByTarget()]] in order
     * to remove all pages and snippets of a given target.
     *
     * @param $targetId
     * @param $type
     * @param ContentContainerActiveRecord|null $container
     * @throws \Throwable
     * @throws \yii\base\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function deleteByTarget($targetId, $type, ContentContainerActiveRecord $container = null)
    {
        foreach ($this->findContentByTarget($targetId, $type, $container)->all() as $content) {
            $content->delete();
        }
    }

    /**
     * Deletes all pages and snippets related to a given target.
     *
     * @param $targetId
     */
    public function deleteAllByTarget($targetId)
    {
        foreach (Page::find()->where(['target' => $targetId])->all() as $page) {
            /* @var Page $page */
            $page->delete();
        }
    }

    /**
     * Returns all pages related to a given target.
     *
     * @param string|Target $targetId
     * @param ContentContainerActiveRecord|null $container
     * @return ActiveQueryContent
     * @throws \yii\base\Exception
     * @throws \Throwable
     */
    public function findContentByTarget($targetId, $type, ContentContainerActiveRecord $container = null)
    {
        if ($targetId instanceof Target) {
            $container = $targetId->container;
            $targetId = $targetId->id;
        }

        $query = Page::find()
            ->where(['target' => $targetId])
            ->contentContainer($container);

        if ($container) {
            // See https://github.com/humhub/humhub/issues/3784 this does not work for global content
            $query->readable();
        } else {
            $query->andWhere($query->stateFilterCondition);
        }

        if (!CustomContentContainer::canSeeAdminOnlyContent($container)) {
            $query->andWhere(['admin_only' => 0]);
        }

        return $query->orderBy([
            Page::tableName() . '.sort_order' => SORT_ASC,
            Page::tableName() . '.id' => SORT_DESC,
        ]);
    }

    /**
     * Should be called to search for a single custom content with a given id.
     *
     * @param $id
     * @param $targetId
     * @param $type
     * @param ContentContainerActiveRecord|null $container
     * @return Page|null
     * @throws \yii\base\Exception
     */
    public function getSingleContent($id, $targetId, $type, ContentContainerActiveRecord $container = null): ?Page
    {
        return $this->findContentByTarget($targetId, $type, $container)
            ->andWhere([Page::tableName() . '.id' => $id])
            ->one();
    }

}
