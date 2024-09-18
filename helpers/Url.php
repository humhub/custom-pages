<?php

namespace humhub\modules\custom_pages\helpers;

use humhub\components\ActiveRecord;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\custom_pages\models\CustomContentContainer;
use humhub\modules\custom_pages\models\PageType;
use humhub\modules\custom_pages\models\Target;
use yii\helpers\Url as BaseUrl;

class Url extends BaseUrl
{

    const ROUTE_CONFIG = '/custom-pages/config';
    const ROUTE_EDIT_PAGE = '/custom-pages/page/edit';

    const ROUTE_PAGE_DELETE = '/custom-pages/page/delete';
    const ROUTE_SNIPPET_DELETE = '/custom-pages/snippet/delete';

    const ROUTE_EDIT_SNIPPET = '/custom-pages/snippet/edit';
    const ROUTE_PAGE_OVERVIEW = '/custom-pages/page';

    const ROUTE_PAGE_ADD = '/custom-pages/page/add';
    const ROUTE_SNIPPET_ADD = '/custom-pages/snippet/add';

    const ROUTE_SNIPPET_OVERVIEW = '/custom-pages/snippet';

    const ROUTE_TEMPLATE_LAYOUT_ADMIN = '/custom-pages/template/layout-admin';

    const ROUTE_PAGE_INLINE_EDIT = '/custom-pages/view';

    const ROUTE_SNIPPET_INLINE_EDIT = '/custom-pages/snippet/edit-snippet';

    public static function toInlineEdit(CustomContentContainer $content, ContentContainerActiveRecord $container = null)
    {
        if($content->getPageType() === PageType::Snippet) {
            return static::create(static::ROUTE_SNIPPET_INLINE_EDIT, ['id' => $content->id], $container);
        } else {
            return static::create(static::ROUTE_PAGE_INLINE_EDIT, ['id' => $content->id, 'editMode' => 1], $container);
        }
    }

    public static function toTemplateLayoutAdmin()
    {
        return static::toRoute(static::ROUTE_TEMPLATE_LAYOUT_ADMIN);
    }

    private static function create($route, $params = [], ContentContainerActiveRecord $container = null)
    {
        if($container) {
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

    public static function toCreatePage($targetId, $pageType, $contentType = null, ContentContainerActiveRecord $container = null)
    {
        if($targetId instanceof Target) {
            $container = $targetId->container;
            $targetId = $targetId->id;
        }

        if($contentType instanceof ContentContainerActiveRecord) {
            $container = $contentType;
            $contentType = null;
        }

        $route = ($pageType === PageType::Page) ? static::ROUTE_EDIT_PAGE : static::ROUTE_EDIT_SNIPPET;

        return static::create($route, ['targetId' => $targetId, 'type' => $contentType], $container);
    }

    public static function toChooseContentType($targetId, $pageType, ContentContainerActiveRecord $container = null)
    {
        if($targetId instanceof Target) {
            $container = $targetId->container;
            $targetId = $targetId->id;
        }

        $route = ($pageType === PageType::Page) ? static::ROUTE_PAGE_ADD : static::ROUTE_SNIPPET_ADD;

        return static::create($route, ['targetId' => $targetId], $container);
    }

    public static function toAddContentType($targetId, $pageType, $contentType, ContentContainerActiveRecord $container = null)
    {
        if($targetId instanceof Target) {
            $container = $targetId->container;
            $targetId = $targetId->id;
        }

        $route = ($pageType === PageType::Page) ? static::ROUTE_PAGE_ADD : static::ROUTE_SNIPPET_ADD;

        return static::create($route, ['targetId' => $targetId, 'type' => $contentType], $container);
    }


    public static function toEditPage($id, ContentContainerActiveRecord $container = null)
    {
        if($id instanceof ActiveRecord) {
            $id = $id->id;
        }

        return static::create(static::ROUTE_EDIT_PAGE, ['id' => $id], $container);
    }

    public static function toEditSnippet($id,  ContentContainerActiveRecord $container = null)
    {
        if($id instanceof ActiveRecord) {
            $id = $id->id;
        }

        return static::create(static::ROUTE_EDIT_SNIPPET, ['id' => $id], $container);
    }

    public static function toPageOverview(ContentContainerActiveRecord $container = null)
    {
        return static::toOverview(PageType::Page, $container);
    }

    public static function toOverview($pageType, ContentContainerActiveRecord $container = null)
    {
        $route = ($pageType === PageType::Page) ? static::ROUTE_PAGE_OVERVIEW : static::ROUTE_SNIPPET_OVERVIEW;
        return static::create($route, [], $container);
    }

    public static function toSnippetOverview(ContentContainerActiveRecord $container = null)
    {
        return static::create(static::ROUTE_SNIPPET_OVERVIEW, [], $container);
    }

    public static function toDeletePage(CustomContentContainer $page, ContentContainerActiveRecord $container = null)
    {
        $route = ($page->getPageType() === PageType::Page) ? static::ROUTE_PAGE_DELETE : static::ROUTE_SNIPPET_DELETE;
        return static::create($route, ['id' => $page->id], $container);
    }

}
