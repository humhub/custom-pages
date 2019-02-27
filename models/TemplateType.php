<?php
/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 13.02.2019
 * Time: 13:29
 */

namespace humhub\modules\custom_pages\models;


use humhub\modules\custom_pages\modules\template\components\TemplateCache;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use humhub\modules\custom_pages\modules\template\models\TemplatePagePermission;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\HttpException;

class TemplateType extends ContentType
{

    const ID = 5;

    function getId()
    {
        return static::ID;
    }

    function getLabel()
    {
        return Yii::t('CustomPagesModule.base', 'Template');
    }

    function getDescription()
    {
       return Yii::t('CustomPagesModule.base', 'Templates allow you to define combinable page fragments with inline edit functionality.');
    }

    /**
     * @param CustomContentContainer $content
     * @param array $options
     * @return string
     */
    public function render(CustomContentContainer $content, $options = [])
    {
        $templateInstance = TemplateInstance::findOne(['object_model' => get_class($content) ,'object_id' => $content->id]);

        if(!$templateInstance) {
            throw new InvalidArgumentException('Template instance not found!');
        }

        $canEdit = TemplatePagePermission::canEdit();
        $editMode = isset($options['editMode'])
            ?  $options['editMode']
            : (bool) Yii::$app->request->get('editMode');

        $editMode = $editMode && $canEdit;

        $html = '';

        if(!$canEdit && TemplateCache::exists($templateInstance)) {
            $html = TemplateCache::get($templateInstance);
        } else {
            $html = $templateInstance->render($editMode);
            if(!$canEdit) {
                TemplateCache::set($templateInstance, $html);
            }
        }
        return $html;
    }
}