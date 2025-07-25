<?php

namespace humhub\modules\custom_pages\helpers;

use humhub\components\ActiveRecord;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\models\Target;
use yii\helpers\Url as BaseUrl;

class Url extends BaseUrl
{
    public const ROUTE_CONFIG = '/custom_pages/config';
    public const ROUTE_VIEW_PAGE = '/custom_pages/view';
    public const ROUTE_EDIT_PAGE = '/custom_pages/page/edit';
    public const ROUTE_COPY_PAGE = '/custom_pages/page/copy';

    public const ROUTE_PAGE_DELETE = '/custom_pages/page/delete';
    public const ROUTE_SNIPPET_DELETE = '/custom_pages/snippet/delete';

    public const ROUTE_EDIT_SNIPPET = '/custom_pages/snippet/edit';
    public const ROUTE_COPY_SNIPPET = '/custom_pages/snippet/copy';
    public const ROUTE_PAGE_OVERVIEW = '/custom_pages/page';

    public const ROUTE_PAGE_ADD = '/custom_pages/page/add';
    public const ROUTE_SNIPPET_ADD = '/custom_pages/snippet/add';

    public const ROUTE_SNIPPET_OVERVIEW = '/custom_pages/snippet';

    public const ROUTE_TEMPLATE_ADMIN = '/custom_pages/template/admin';

    public const ROUTE_SNIPPET_INLINE_EDIT = '/custom_pages/snippet/edit-snippet';

    public static function toInlineEdit(CustomPage $page, ?ContentContainerActiveRecord $container = null)
    {
        if ($page->getPageType() === PageType::Snippet) {
            return static::create(static::ROUTE_SNIPPET_INLINE_EDIT, ['id' => $page->id], $container);
        } else {
            return static::create(static::ROUTE_VIEW_PAGE, ['id' => $page->id, 'mode' => 'edit'], $container);
        }
    }

    public static function toTemplateAdmin()
    {
        return static::toRoute(static::ROUTE_TEMPLATE_ADMIN);
    }

    private static function create($route, $params = [], ?ContentContainerActiveRecord $container = null)
    {
        if ($container) {
            return $container->createUrl($route, $params);
        } else {
            $params[0] = $route;
            return  static::to($params);
        }
    }
    /**
     * @return string
     */
    public static function toModuleConfig()
    {
        return static::toRoute(static::ROUTE_CONFIG);
    }

    public static function toCreatePage($targetId, $pageType, $contentType = null, ?ContentContainerActiveRecord $container = null)
    {
        if ($targetId instanceof Target) {
            $container = $targetId->container;
            $targetId = $targetId->id;
        }

        if ($contentType instanceof ContentContainerActiveRecord) {
            $container = $contentType;
            $contentType = null;
        }

        $route = ($pageType === PageType::Page) ? static::ROUTE_EDIT_PAGE : static::ROUTE_EDIT_SNIPPET;

        return static::create($route, ['targetId' => $targetId, 'type' => $contentType], $container);
    }

    public static function toChooseContentType($targetId, $pageType, ?ContentContainerActiveRecord $container = null)
    {
        if ($targetId instanceof Target) {
            $container = $targetId->container;
            $targetId = $targetId->id;
        }

        $route = ($pageType === PageType::Page) ? static::ROUTE_PAGE_ADD : static::ROUTE_SNIPPET_ADD;

        return static::create($route, ['targetId' => $targetId], $container);
    }

    public static function toAddContentType($targetId, $pageType, $contentType, ?ContentContainerActiveRecord $container = null)
    {
        if ($targetId instanceof Target) {
            $container = $targetId->container;
            $targetId = $targetId->id;
        }

        $route = ($pageType === PageType::Page) ? static::ROUTE_PAGE_ADD : static::ROUTE_SNIPPET_ADD;

        return static::create($route, ['targetId' => $targetId, 'type' => $contentType], $container);
    }

    public static function toViewPage($id, ?ContentContainerActiveRecord $container = null)
    {
        if ($id instanceof ActiveRecord) {
            $id = $id->id;
        }

        return static::create(static::ROUTE_VIEW_PAGE, ['id' => $id], $container);
    }

    public static function toEditPage($id, ?ContentContainerActiveRecord $container = null)
    {
        if ($id instanceof ActiveRecord) {
            $id = $id->id;
        }

        return static::create(static::ROUTE_EDIT_PAGE, ['id' => $id], $container);
    }

    public static function toCopyPage(CustomPage $page): string
    {
        $route = $page->isSnippet() ? static::ROUTE_COPY_SNIPPET : static::ROUTE_COPY_PAGE;
        return static::create($route, ['id' => $page->id], $page->content->container);
    }

    public static function toEditSnippet($id, ?ContentContainerActiveRecord $container = null)
    {
        if ($id instanceof ActiveRecord) {
            $id = $id->id;
        }

        return static::create(static::ROUTE_EDIT_SNIPPET, ['id' => $id], $container);
    }

    public static function toPageOverview(?ContentContainerActiveRecord $container = null)
    {
        return static::toOverview(PageType::Page, $container);
    }

    public static function toOverview($pageType, ?ContentContainerActiveRecord $container = null)
    {
        $route = ($pageType === PageType::Page) ? static::ROUTE_PAGE_OVERVIEW : static::ROUTE_SNIPPET_OVERVIEW;
        return static::create($route, [], $container);
    }

    public static function toSnippetOverview(?ContentContainerActiveRecord $container = null)
    {
        return static::create(static::ROUTE_SNIPPET_OVERVIEW, [], $container);
    }

    public static function toDeletePage(CustomPage $page, ?ContentContainerActiveRecord $container = null)
    {
        $route = ($page->getPageType() === PageType::Page) ? static::ROUTE_PAGE_DELETE : static::ROUTE_SNIPPET_DELETE;
        return static::create($route, ['id' => $page->id], $container);
    }

}
