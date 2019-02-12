<?php

namespace humhub\modules\custom_pages\helpers;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\custom_pages\models\Target;
use yii\helpers\Url as BaseUrl;

class Url extends BaseUrl
{

    const ROUTE_CONFIG = '/custom_pages/config';
    const ROUTE_EDIT_PAGE = '/custom_pages/page/edit';
    const ROUTE_EDIT_SNIPPET = '/custom_pages/snippet/edit';

    const ROUTE_PAGE_OVERVIEW = '/custom_pages/page';
    const ROUTE_SNIPPET_OVERVIEW = '/custom_pages/snippet';

    private static function create($route, ContentContainerActiveRecord $container = null)
    {
        return $container ? $container->createUrl($route) : static::to($route);
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

        return static::create([static::ROUTE_EDIT_PAGE, 'targetId' => $targetId, 'type' => $contentType], $container);
    }

    public static function toEditPage($id, ContentContainerActiveRecord $container = null)
    {
        return static::create([static::ROUTE_EDIT_PAGE, 'id' => $id], $container);
    }

    public static function toPageOverview(ContentContainerActiveRecord $container = null)
    {
        return static::create([static::ROUTE_PAGE_OVERVIEW], $container);
    }

    public static function toSnippetOverview(ContentContainerActiveRecord $container = null)
    {
        return static::create([static::ROUTE_SNIPPET_OVERVIEW], $container);
    }
}