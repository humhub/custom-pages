<?php


namespace humhub\modules\custom_pages\modules\template\components;


use yii\web\HttpException;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;

class TemplateRenderer
{
    /**
     * @param $page
     * @param bool $editMode
     * @return string
     * @throws HttpException
     */
    public static function render($page, $editMode = false)
    {
        $templateInstance = TemplateInstance::findOne(['object_model' => get_class($page) ,'object_id' => $page->id]);

        if(!$templateInstance) {
            throw new HttpException(404, 'Template instance not found!');
        }

        $html = '';

        if(!$editMode && TemplateCache::exists($templateInstance)) {
            $html = TemplateCache::get($templateInstance);
        } else {
            $html = $templateInstance->render($editMode);
            if(!$editMode) {
                TemplateCache::set($templateInstance, $html);
            }
        }
        return $html;
    }

}