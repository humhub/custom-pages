<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2016 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\components;

use humhub\modules\custom_pages\modules\template\elements\BaseTemplateElementContent;
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
    }

    /**
     * Flushes all cache entries related to a given $ownerContent instance.
     *
     * @param BaseTemplateElementContent $elementContent
     * @return void
     */
    public static function flushByElementContent(BaseTemplateElementContent $elementContent): void
    {
        $templateInstance = $elementContent->templateInstance;
        $templateInstance && self::flushByTemplateInstance($templateInstance);
    }

    /**
     * Flushes all cache entries related to an template instance.
     *
     * @param TemplateInstance|null $templateInstance
     * @return void
     */
    public static function flushByTemplateInstance(?TemplateInstance $templateInstance): void
    {
        $templateInstance && Yii::$app->cache->delete($templateInstance->getRoot()->getCacheKey());
    }
}
