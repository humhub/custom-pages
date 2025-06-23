<?php

/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 13.02.2019
 * Time: 13:29
 */

namespace humhub\modules\custom_pages\types;

use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\modules\template\elements\BaseElementContent;
use humhub\modules\custom_pages\modules\template\elements\ContainerElement;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use humhub\modules\custom_pages\modules\template\services\TemplateInstanceRendererService;
use Yii;
use yii\widgets\ActiveForm;

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
        return TemplateInstanceRendererService::instance($content)->render();
    }

    public function getViewName(): string
    {
        return 'template';
    }

    public function renderFormField(ActiveForm $form, CustomPage $page): string
    {
        return $form->field($page, 'templateId')->dropDownList($page->getAllowedTemplateSelection(), ['value' => $page->getTemplateId(), 'disabled' => !$page->isNewRecord || Yii::$app->controller->action->id === 'copy']);
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

    /**
     * @inheritdoc
     */
    public function beforeDuplicate(CustomPage $newPage): bool
    {
        $newPage->templateId = $this->customPage->getTemplateId();

        return parent::beforeDuplicate($newPage);
    }

    /**
     * @inheritdoc
     */
    public function duplicate(?array $loadData = null): CustomPage
    {
        $newPage = parent::duplicate($loadData);

        if (!$newPage->isNewRecord) {
            $templateInstance = TemplateInstance::findByOwner($newPage);
            if ($templateInstance !== null) {
                $this->duplicateElementContents($templateInstance->id);
            }
        }

        return $newPage;
    }

    /**
     * Duplicate Template Element Contents linked to the Custom Page
     *
     * @param int $newTemplateInstanceId
     * @param int|null $containerItemId
     * @return void
     * @throws \yii\db\Exception
     */
    protected function duplicateElementContents(int $newTemplateInstanceId, int $containerItemId = null): void
    {
        $elementContents = BaseElementContent::find()
            ->leftJoin('custom_pages_template_instance', 'template_instance_id = custom_pages_template_instance.id')
            ->where(['page_id' => $this->customPage->id])
            ->andWhere(['container_item_id' => $containerItemId]);

        foreach ($elementContents->each() as $elementContent) {
            /* @var BaseElementContent $elementContent */
            $copyElementContent = clone $elementContent;
            $copyElementContent->id = null;
            $copyElementContent->setIsNewRecord(true);
            $copyElementContent->template_instance_id = $newTemplateInstanceId;
            if ($copyElementContent->save() && $elementContent instanceof ContainerElement) {
                foreach ($elementContent->items as $item) {
                    $copyItem = clone $item;
                    $copyItem->id = null;
                    $copyItem->setIsNewRecord(true);
                    $copyItem->element_content_id = $copyElementContent->id;
                    if ($copyItem->save()) {
                        $this->duplicateElementContents($copyItem->templateInstance->id, $item->id);
                    }
                }
            }
        }
    }
}
