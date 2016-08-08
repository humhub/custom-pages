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

class TemplateCache
{
    public static function flushByTemplateId($templateId)
    {   
        foreach(TemplateInstance::findByTemplateId($templateId)->all() as $templateInstance) {
            self::flushByTemplateInstance($templateInstance);
        }
        
        foreach(ContainerContentItem::findByTemplateId($templateId)->all() as $containerItem) {
            $ownerContent = OwnerContent::findByContent($containerItem->container);
            self::flushByOwnerContent($ownerContent);
        }
    }
   
    public static function flushByOwnerContent(OwnerContent $ownerContent)
    {
        $owner = null;
        
        while(!$owner instanceof TemplateInstance) {
            $owner = $ownerContent->owner;
            
            if($owner instanceof ContainerContentItem) {
                $ownerContent = OwnerContent::findByContent($owner->container);
            } elseif (!$owner instanceof TemplateInstance) {
                // Just to avoid infinity loops in case of invalid data.
                return;
            }
        }
        
        self::flushByTemplateInstance($owner);
    }

    public static function flushByTemplateInstance(TemplateInstance $owner)
    {
        Yii::$app->cache->delete(self::getKey($owner));
    }
    
    public static function getKey(TemplateInstance $owner)
    {
        return $owner->className().$owner->getPrimaryKey();
    }
    
     public static function exists($owner)
    {
        return Yii::$app->cache->exists(self::getKey($owner));
    }
    
    public static function get($owner)
    {
        return Yii::$app->cache->get(self::getKey($owner));
    }
    
    public static function set($owner, $content)
    {
        return Yii::$app->cache->set(self::getKey($owner), $content);
    }
    
}