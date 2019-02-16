<?php

namespace humhub\modules\custom_pages\helpers;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\custom_pages\models\Target;
use yii\helpers\Url as BaseUrl;

class Url extends BaseUrl
{

    const ROUTE_CONFIG = '/custom_pages/config';
    const ROUTE_EDIT_PAGE = '/custom_pages/page/edit';
    const ROUTE_PAGE_DELETE = '/custom_pages/page/delete';

    const ROUTE_EDIT_SNIPPET = '/custom_pages/snippet/edit';
    const ROUTE_PAGE_OVERVIEW = '/custom_pages/page';
    const ROUTE_PAGE_ADD = '/custom_pages/page/add';
    const ROUTE_SNIPPET_OVERVIEW = '/custom_pages/snippet';

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

    public static function toCreatePage($targetId, $contentType = null, ContentContainerActiveRecord $container = null)
    {
        if($targetId instanceof Target) {
            $container = $targetId->container;
            $targetId = $targetId->id;
        }

        return static::create(static::ROUTE_EDIT_PAGE, ['targetId' => $targetId, 'type' => $contentType], $container);
    }

    public static function toChooseContentType($targetId, ContentContainerActiveRecord $container = null)
    {
        if($targetId instanceof Target) {
            $container = $targetId->container;
            $targetId = $targetId->id;
        }

        return static::create(static::ROUTE_PAGE_ADD, ['targetId' => $targetId], $container);
    }

    public static function toAddContentType($targetId, $contentType, ContentContainerActiveRecord $container = null)
    {
        if($targetId instanceof Target) {
            $container = $targetId->container;
            $targetId = $targetId->id;
        }

        return static::create(static::ROUTE_PAGE_ADD, ['targetId' => $targetId, 'type' => $contentType], $container);
    }


    public static function toEditPage($id, ContentContainerActiveRecord $container = null)
    {
        return static::create(static::ROUTE_EDIT_PAGE, ['id' => $id], $container);
    }

    public static function toPageOverview(ContentContainerActiveRecord $container = null)
    {
        return static::create(static::ROUTE_PAGE_OVERVIEW, [], $container);
    }

    public static function toSnippetOverview(ContentContainerActiveRecord $container = null)
    {
        return static::create(static::ROUTE_SNIPPET_OVERVIEW, [], $container);
    }

    public static function toDeletePage($pageId, ContentContainerActiveRecord $container = null)
    {
       if(!is_numeric($pageId)) {
           $pageId = $pageId->id;
       }
        return static::create(static::ROUTE_PAGE_DELETE, ['id' => $pageId], $container);
    }
}