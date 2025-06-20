<?php

namespace humhub\modules\custom_pages\interfaces;

use humhub\modules\content\components\ActiveQueryContent;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\helpers\PageType;
use humhub\modules\custom_pages\models\Target;
use humhub\modules\custom_pages\modules\template\elements\BaseElementContent;
use humhub\modules\custom_pages\modules\template\elements\ContainerElement;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use humhub\modules\custom_pages\types\TemplateType;
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
        foreach ($this->findByTarget($targetId, $container)->each() as $content) {
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
     * Returns a query to find all pages from the given container/space or global pages.
     *
     * @param ContentContainerActiveRecord|null $container
     * @return ActiveQueryContent
     * @throws \Throwable
     */
    public function find(?ContentContainerActiveRecord $container = null): ActiveQueryContent
    {
        $query = CustomPage::find()
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

    /**
     * Returns a query to find all pages related to a given target.
     *
     * @param string|Target $targetId
     * @param ContentContainerActiveRecord|null $container
     * @return ActiveQueryContent
     * @throws \Throwable
     */
    public function findByTarget($targetId, ?ContentContainerActiveRecord $container = null): ActiveQueryContent
    {
        if ($targetId instanceof Target) {
            $container = $targetId->container;
            $targetId = $targetId->id;
        }

        return $this->find($container)
            ->andWhere([CustomPage::tableName() . '.target' => $targetId]);
    }

    /**
     * Returns a query to find all pages by page type(page or snippet).
     *
     * @param string $pageType
     * @param ContentContainerActiveRecord|null $container
     * @return ActiveQueryContent
     * @throws \Throwable
     */
    public function findByPageType(string $pageType, ?ContentContainerActiveRecord $container = null): ActiveQueryContent
    {
        $targets = $this->getTargets($pageType, $container);

        return $this->find($container)
            ->andWhere([CustomPage::tableName() . '.target' => array_column($targets, 'id')]);
    }

    /**
     * @param CustomPage $sourcePage
     * @return CustomPage|null
     */
    public function duplicatePage(CustomPage $sourcePage, ?array $loadData = null): ?CustomPage
    {
        $newPage = new CustomPage([
            'type' => $sourcePage->type,
            'target' => $sourcePage->target,
        ]);

        $newPage->visibility = $sourcePage->visibility;
        if (TemplateType::isType($sourcePage->type)) {
            $newPage->templateId = $sourcePage->getTemplateId();
        }

        foreach ($sourcePage->attributes as $attrKey => $attrValue) {
            if ($attrKey !== 'id') {
                $newPage->$attrKey = $attrValue;
            }
        }

        foreach ($sourcePage->content->attributes as $attrKey => $attrValue) {
            if (!in_array($attrKey, ['id', 'guid', 'object_model', 'object_id', 'created_at', 'created_by', 'updated_at', 'updated_by'])) {
                $newPage->content->$attrKey = $attrValue;
            }
        }

        if (is_array($loadData) && !$newPage->load($loadData)) {
            return $newPage;
        }


        if (!$newPage->save()) {
            return $newPage;
        }

        if (!empty($newPage->url) && $newPage->url === $sourcePage->url) {
            // Make URL unique
            $newPage->updateAttributes(['url' => $newPage->url . '-' . $newPage->id]);
        }

        if (TemplateType::isType($sourcePage->type)) {
            $templateInstance = TemplateInstance::findByOwner($newPage);
            if ($templateInstance) {
                $this->duplicateElementContents($sourcePage, $templateInstance->id);
            }
        }

        return $newPage;
    }

    public function duplicateElementContents(CustomPage $sourcePage, int $newTemplateInstanceId, int $containerItemId = null): void
    {
        $elementContents = BaseElementContent::find()
            ->leftJoin('custom_pages_template_instance', 'template_instance_id = custom_pages_template_instance.id')
            ->where(['page_id' => $sourcePage->id])
            ->andWhere(['container_item_id' => $containerItemId]);

        foreach ($elementContents->each() as $elementContent) {
            /* @var BaseElementContent $elementContent */
            $copyElementContent = clone $elementContent;
            $copyElementContent->id = null;
            $copyElementContent->setIsNewRecord(true);
            $copyElementContent->template_instance_id = $newTemplateInstanceId;
            if ($copyElementContent->save() && $elementContent instanceof ContainerElement) {
                foreach ($elementContent->items as $item) {
                    $copyItem = clone $item;
                    $copyItem->id = null;
                    $copyItem->setIsNewRecord(true);
                    $copyItem->element_content_id = $copyElementContent->id;
                    if ($copyItem->save()) {
                        $this->duplicateElementContents($sourcePage, $copyItem->templateInstance->id, $item->id);
                    }
                }
            }
        }
    }
}
