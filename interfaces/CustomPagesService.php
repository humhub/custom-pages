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
use humhub\modules\custom_pages\modules\template\models\PagePermission;
use humhub\modules\space\models\Space;
use Yii;
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

    private static $pageTargetCache = [];
    private static $snippetTargetCache = [];

    /**
     * Fetches all available navigations for a given container.
     *
     * @param string $type
     * @param ContentContainerActiveRecord $container
     * @return array
     */
    public function getTargets($type, ContentContainerActiveRecord $container = null)
    {
        $containerKey = $container ? $container->contentcontainer_id : 'global';
        $cache = ($type === PageType::Page) ? static::$pageTargetCache : static::$snippetTargetCache;

        if (isset($cache[$containerKey])) {
            return $cache[$containerKey];
        }

        $event = new CustomPagesTargetEvent(['type' => $type, 'container' => $container]);
        $this->addDefaultTargets($event);

        $this->trigger(self::EVENT_FETCH_TARGETS, $event);

        return $cache[$containerKey] = $event->getTargets();
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
                    $event->addTargets(Snippet::getDefaultTargets());
                } else {
                    $event->addTargets(ContainerSnippet::getDefaultTargets());
                }
        }
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
        foreach (Page::find()->where(['target' => $targetId])->all() as $content) {
            $content->delete();
        }

        foreach (ContainerPage::find()->where(['target' => $targetId])->all() as $content) {
            $content->delete();
        }

        foreach (Snippet::find()->where(['target' => $targetId])->all() as $content) {
            $content->delete();
        }

        foreach (ContainerSnippet::find()->where(['target' => $targetId])->all() as $content) {
            $content->delete();
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

        $contentClass = $this->getContentClass($type, $container);

        /* @var $query ActiveQueryContent */
        $query = call_user_func($contentClass.'::find');

        $query->where(['target' => $targetId]);

        if($container) {
            $query->contentContainer($container);

            // See https://github.com/humhub/humhub/issues/3784 this does not work for global content
            $query->readable();
        }

        if(!CustomContentContainer::canSeeAdminOnlyContent($container)) {
            $query->andWhere(['admin_only' => 0]);
        }

        return $query->orderBy('sort_order, id DESC');
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