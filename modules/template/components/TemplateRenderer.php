<?php

namespace humhub\modules\custom_pages\modules\template\components;

use humhub\modules\custom_pages\helpers\Html;
use yii\web\HttpException;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;

class TemplateRenderer
{
    /**
     * @param $page
     * @param bool $editMode
     * @param bool $applyScriptNonce
     * @param bool $ignoreCache
     * @return string
     * @throws HttpException
     */
    public static function render($page, $editMode = false, bool $applyScriptNonce = true, bool $ignoreCache = false)
    {
        $templateInstance = TemplateInstance::findOne(['object_model' => get_class($page) ,'object_id' => $page->id]);

        if (!$templateInstance) {
            throw new HttpException(404, 'Template instance not found!');
        }

        if (!$ignoreCache && !$editMode && TemplateCache::exists($templateInstance)) {
            $html = TemplateCache::get($templateInstance);
        } else {
            $html = $templateInstance->render($editMode);
            if (!$editMode) {
                TemplateCache::set($templateInstance, $html);
            }
        }

        return $applyScriptNonce ? Html::applyScriptNonce($html) : $html;
    }

}
