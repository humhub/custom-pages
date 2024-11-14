<?php

namespace humhub\modules\custom_pages\interfaces;

use humhub\modules\content\components\ActiveQueryContent;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\helpers\PageType;
use humhub\modules\custom_pages\models\Target;
use yii\base\Component;
use yii\base\StaticInstanceTrait;

/**
 * Class CustomPagesService
 * @package humhub\modules\custom_pages\interfaces
 */
class CustomPagesService extends Component
{
    use StaticInstanceTrait;

    public const EVENT_FETCH_TARGETS = 'fetchTargets';

    private array $cache = [];

    /**
     * Fetches all available navigations for a given container.
     *
     * @param string $type
     * @param ContentContainerActiveRecord|null $container
     * @return Target[]
     */
    public function getTargets(string $type, ?ContentContainerActiveRecord $container = null): array
    {
        $containerKey = $container ? $container->contentcontainer_id : 'global';

        if (!isset($this->cache[$type][$containerKey])) {
            $event = new CustomPagesTargetEvent(['type' => $type, 'container' => $container]);
            $event->addDefaultTargets();

            $this->trigger(self::EVENT_FETCH_TARGETS, $event);

            $this->cache[$type][$containerKey] = $event->getTargets();
        }

        return $this->cache[$type][$containerKey];
    }

    /**
     * @param string $targetId
     * @param string $type
     * @param ContentContainerActiveRecord|null $container
     * @return Target
     */
    public function getTargetById($targetId, $type, ?ContentContainerActiveRecord $container = null): ?Target
    {
        $availableTargets = $this->getTargets($type, $container);
        return array_key_exists($targetId, $availableTargets) ? $availableTargets[$targetId] : null;
    }

    public function getTargetByPage(CustomPage $page): ?Target
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
     * @param ContentContainerActiveRecord|null $container
     * @throws \Throwable
     * @throws \yii\base\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function deleteByTarget($targetId, ContentContainerActiveRecord $container = null): void
    {
        foreach ($this->find($targetId, $container)->each() as $content) {
            $content->delete();
        }
    }

    /**
     * Deletes all pages and snippets related to a given target.
     *
     * @param $targetId
     */
    public function deleteAllByTarget($targetId): void
    {
        foreach (CustomPage::find()->where(['target' => $targetId])->each() as $page) {
            /* @var CustomPage $page */
            $page->delete();
        }
    }

    /**
     * Returns a query to find all pages related to a given target and container/space.
     *
     * @param string|Target $targetId
     * @param ContentContainerActiveRecord|null $container
     * @return ActiveQueryContent
     * @throws \yii\base\Exception
     * @throws \Throwable
     */
    public function find($targetId, ?ContentContainerActiveRecord $container = null): ActiveQueryContent
    {
        if ($targetId instanceof Target) {
            $container = $targetId->container;
            $targetId = $targetId->id;
        }

        $query = CustomPage::find()
            ->where([CustomPage::tableName() . '.target' => $targetId])
            ->contentContainer($container);

        if ($container) {
            // See https://github.com/humhub/humhub/issues/3784 this does not work for global content
            $query->readable();
        } else {
            $query->andWhere($query->stateFilterCondition);
        }

        if (!CustomPage::canSeeAdminOnlyContent($container)) {
            $query->andWhere([CustomPage::tableName() . '.admin_only' => 0]);
        }

        return $query->orderBy([
            CustomPage::tableName() . '.sort_order' => SORT_ASC,
            CustomPage::tableName() . '.id' => SORT_DESC,
        ]);
    }
}
