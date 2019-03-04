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
use humhub\modules\custom_pages\modules\template\models\PagePermission;
use yii\widgets\ActiveForm;
use Yii;
use yii\base\InvalidArgumentException;

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
     * @param CustomContentContainer $page
     * @param bool $insert
     * @param array $changedAttributes
     * @return bool
     */
    public function afterSave($page, $insert, $changedAttributes)
    {
        if($insert) {
            $templateInstance = new TemplateInstance([
                'object_model' => get_class($page),
                'object_id' => $page->id,
                'template_id' => $page->templateId
            ]);
            return $templateInstance->save();
        }

        return true;

    }

    public function afterDelete($page)
    {
        TemplateInstance::deleteByOwner($page);
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

        $canEdit = PagePermission::canEdit();
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

    public function getViewName()
    {
        return 'template';
    }

    public function renderFormField(ActiveForm $form, CustomContentContainer $page)
    {
        return $form->field($page, 'templateId')->dropDownList($page->getAllowedTemplateSelection(), ['value' => $page->getTemplateId(), 'disabled' => !$page->isNewRecord]);
    }
}