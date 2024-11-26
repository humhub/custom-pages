<?php

/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 13.02.2019
 * Time: 13:29
 */

namespace humhub\modules\custom_pages\types;

use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\modules\template\components\TemplateCache;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use humhub\modules\custom_pages\modules\template\models\PagePermission;
use Yii;
use yii\widgets\ActiveForm;
use yii\base\InvalidArgumentException;

class TemplateType extends ContentType
{
    public const ID = 5;

    public function getLabel(): string
    {
        return Yii::t('CustomPagesModule.base', 'Template');
    }

    public function getDescription(): string
    {
        return Yii::t('CustomPagesModule.base', 'Templates allow you to define combinable page fragments with inline edit functionality.');
    }

    public function render(CustomPage $content, $options = []): string
    {
        $templateInstance = TemplateInstance::findOne(['page_id' => $content->id]);

        if (!$templateInstance) {
            throw new InvalidArgumentException('Template instance not found!');
        }

        $canEdit = PagePermission::canEdit();
        $editMode = $options['editMode'] ?? (bool) Yii::$app->request->get('editMode');

        $editMode = $editMode && $canEdit;

        if (!$canEdit && TemplateCache::exists($templateInstance)) {
            $html = TemplateCache::get($templateInstance);
        } else {
            $html = $templateInstance->render($editMode);
            if (!$canEdit) {
                TemplateCache::set($templateInstance, $html);
            }
        }
        return $html;
    }

    public function getViewName(): string
    {
        return 'template';
    }

    public function renderFormField(ActiveForm $form, CustomPage $page): string
    {
        return $form->field($page, 'templateId')->dropDownList($page->getAllowedTemplateSelection(), ['value' => $page->getTemplateId(), 'disabled' => !$page->isNewRecord]);
    }

    /**
     * @inheritdoc
     */
    public function afterSave(CustomPage $page, bool $insert, array $changedAttributes): bool
    {
        if (!parent::afterSave($page, $insert, $changedAttributes)) {
            return false;
        }

        if ($insert) {
            $templateInstance = new TemplateInstance([
                'page_id' => $page->id,
                'template_id' => $page->templateId,
            ]);
            return $templateInstance->save();
        }

        return true;
    }

    public function afterDelete(CustomPage $page): void
    {
        TemplateInstance::deleteByOwner($page);
    }
}
