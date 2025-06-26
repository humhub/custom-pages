<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\ui\form\widgets\MultiSelect;
use Yii;
use yii\db\ActiveQuery;

/**
 * Class to manage content records of the Container elements
 *
 * @property-read ContainerItem[] $items
 * @property-read Template[] $templates
 * @property-read Template[] $allowedTemplates
 * @property-read ContainerDefinition $definition
 */
class ContainerElement extends BaseElementContent
{
    /**
     * @inheritdoc
     */
    public function getLabel(): string
    {
        return Yii::t('CustomPagesModule.template', 'Container');
    }

    /**
     * @inheritdoc
     */
    public $definitionModel = ContainerDefinition::class;

    /**
     * @inheritdoc
     */
    protected function getDynamicAttributes(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function validate($attributeNames = null, $clearErrors = true)
    {
        return parent::validate($attributeNames, $clearErrors) && $this->definition->validate();
    }

    public function getAllowedTemplates(): array
    {
        return $this->definition->allowedTemplates;
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if ($this->hasItems()) {
            foreach ($this->items as $item) {
                $item->delete();
            }
        }

        return parent::beforeDelete();
    }

    public function moveItem($itemId, $step)
    {
        $item = ContainerItem::findOne(['id' => $itemId]);

        if ($item == null || $item->element_content_id != $this->id) {
            return;
        }

        $nextIndex = $this->getNextIndex();

        // If move up and item is not last
        if ($step > 0 && $item->sort_order != $nextIndex - 1) {
            $oldIndex = $item->sort_order;
            $newIndex = $oldIndex + $step;
            $item->sort_order = ($newIndex < $nextIndex) ? $newIndex : ($nextIndex - 1);

            ContainerItem::decrementBetween($this->id, $oldIndex, $item->sort_order);

            $item->save();
        } elseif ($step < 0 && $item->sort_order != 0) {
            $oldIndex = $item->sort_order;
            $newIndex = $oldIndex + $step;
            $item->sort_order = ($newIndex > 0) ? $newIndex : 0;

            ContainerItem::incrementBetween($this->id, $item->sort_order, $oldIndex);

            $item->save();
        }
    }

    public function createEmptyItem($templateId, $index = null): ContainerItem
    {
        $index = ($index == null) ? $this->getNextIndex() : $index;

        $item = new ContainerItem();
        $item->pageId = $this->templateInstance->page_id;
        $item->templateId = $templateId;
        $item->element_content_id = $this->id;
        $item->sort_order = $index;
        return $item;
    }

    public function getNextIndex()
    {
        return $this->getItems()->count();
    }

    public function hasItems(): bool
    {
        return $this->getItems()->count() > 0;
    }

    public function getItems(): ActiveQuery
    {
        return $this->hasMany(ContainerItem::class, ['element_content_id' => 'id'])->orderBy('sort_order ASC');
    }

    public function canAddItem(): bool
    {
        return $this->definition === null || $this->definition->allow_multiple || !$this->hasItems();
    }

    public function getTemplates(): array
    {
        return $this->definition->allowedTemplates;
    }

    public function isSingleAllowedTemplate(): bool
    {
        return $this->definition->isSingleAllowedTemplate();
    }

    /**
     * @inheritdoc
     */
    public function isCacheable(): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getTemplateVariable(): BaseElementVariable
    {
        return new ContainerElementVariable($this);
    }

    /**
     * @inheritdoc
     */
    public function renderEditForm(ActiveForm $form): string
    {
        $disableDefinition = !$this->isAdminEditMode() && !$this->definition->isNewRecord;

        return $form->field($this->definition, 'templates')->widget(MultiSelect::class, [
                'items' => $this->definition->getAllowedTemplateOptions(),
                'disabled' => $disableDefinition,
            ]) .
            $form->field($this->definition, 'allow_multiple')->checkbox(['disabled' => $disableDefinition]);
    }
}
