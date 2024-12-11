<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2016 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\components;

use humhub\modules\custom_pages\modules\template\models\ContainerContentItem;
use humhub\modules\custom_pages\modules\template\models\OwnerContent;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use Yii;

/**
 * Used to manage the template cache of template pages.
 */
class TemplateCache
{
    /**
     * Flushes all cache entries related to a given template (identified by $templateId)
     *
     * @param int $templateId
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
     * @param TemplateInstance $templateInstance
     */
    public static function flushByTemplateInstance(TemplateInstance $templateInstance)
    {
        Yii::$app->cache->delete($templateInstance->getCacheKey());
    }
}
