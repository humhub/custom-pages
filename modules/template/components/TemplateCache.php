<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2016 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\components;

use Yii;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use humhub\modules\custom_pages\modules\template\models\ContainerContentItem;
use humhub\modules\custom_pages\modules\template\models\OwnerContent;

/**
 * Used to manage the template cache of template pages.
 */
class TemplateCache
{

    /**
     * Flushes all cache entries related to a given template (identified by $templateId)
     * 
     * @param integer $templateId
     */
    public static function flushByTemplateId($templateId)
    {
        foreach (TemplateInstance::findByTemplateId($templateId)->all() as $templateInstance) {
            self::flushByTemplateInstance($templateInstance);
        }

        foreach (ContainerContentItem::findByTemplateId($templateId)->all() as $containerItem) {
            $ownerContent = OwnerContent::findByContent($containerItem->container);
            self::flushByOwnerContent($ownerContent);
        }
    }

    /**
     * Flushes all cache entries related to a given $ownerContent instance.
     * 
     * @param OwnerContent $ownerContent
     * @return null
     */
    public static function flushByOwnerContent(OwnerContent $ownerContent)
    {
        $owner = null;

        while (!$owner instanceof TemplateInstance) {
            $owner = $ownerContent->owner;

            if ($owner instanceof ContainerContentItem) {
                $ownerContent = OwnerContent::findByContent($owner->container);
            } elseif (!$owner instanceof TemplateInstance) {
                // Just to avoid infinity loops in case of invalid data.
                return;
            }
        }

        self::flushByTemplateInstance($owner);
    }

    /**
     * Flushes all cache entries related to an template instance.
     * 
     * @param TemplateInstance $owner
     */
    public static function flushByTemplateInstance(TemplateInstance $owner)
    {
        Yii::$app->cache->delete(self::getKey($owner));
    }

    /**
     * Returns the template key for a given template instance.
     * 
     * @param TemplateInstance $owner
     * @return string
     */
    public static function getKey(TemplateInstance $owner)
    {
        return get_class($owner) . $owner->getPrimaryKey();
    }

    /**
     * Checks for an existing cache entrie for a given $owner instance exists.
     * @param TemplateInstance $owner
     * @return bool
     */
    public static function exists($owner)
    {
        return Yii::$app->cache->exists(self::getKey($owner));
    }

    /**
     * Retrieves the cached content for a given $owner instnance.
     * @param TemplateInstance $owner
     * @return string
     */
    public static function get($owner)
    {
        return Yii::$app->cache->get(self::getKey($owner));
    }

    /**
     * Sets the cache entry for a given $owner instance.
     * 
     * @param TemplateInstance $owner
     * @param string $content
     * @return bool
     */
    public static function set($owner, $content)
    {
        return Yii::$app->cache->set(self::getKey($owner), $content);
    }

}
