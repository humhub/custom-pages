<?php

namespace humhub\modules\custom_pages\modules\template\models;

use Yii;
use humhub\modules\custom_pages\modules\template\models\TemplateContentActiveRecord;
use humhub\modules\custom_pages\modules\template\widgets\TemplateContentFormFields;

/**
 * This is the model class for table "custom_pages_page".
 *
 * The followings are the available columns in table 'custom_pages_page':
 */
class ContainerContent extends TemplateContentActiveRecord
{

    public static $label = 'Container';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->definitionModel = ContainerContentDefinition::class;
    }

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'custom_pages_template_container_content';
    }

    public function validate($attributeNames = null, $clearErrors = true)
    {
        return parent::validate() && $this->definition->validate();
    }

    public function getAllowedTemplates()
    {
        if (empty($this->definition->templates)) {
            return Template::findAllByType(Template::TYPE_CONTAINER);
        }
        return $this->definition->templates;
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (!empty($this->allowedTemplateSelection)) {
            ContainerContentTemplate::deleteAll(['container_content_id' => $this->id]);
            foreach ($this->allowedTemplateSelection as $allowedTemplateId) {
                $allowedTemplate = new ContainerContentTemplate();
                $allowedTemplate->template_id = $allowedTemplateId;
                $allowedTemplate->container_content_id = $this->id;
                $allowedTemplate->save();
            }
        }

        parent::afterSave($insert, $changedAttributes);
    }

    public function beforeDelete()
    {
        if ($this->hasItems()) {
            foreach ($this->items as $item) {
                $item->delete();
            }
        }

        return parent::beforeDelete();
    }

    public function getLabel()
    {
        return self::$label;
    }

    public function copy()
    {
        // We do not have to set additional attributes here
        return $this->createCopy();
    }

    public function render($options = [])
    {

        $items = $this->items;

        $result = '';

        $editMode = isset($options['editMode']) ? $options['editMode'] : false;

        if (empty($items) && $editMode) {
            return $this->renderEmpty($options);
        }

        foreach ($this->items as $containerItem) {
            $result .= $containerItem->render($editMode, $this->definition->is_inline);
        }

        if ($this->isEditMode($options)) {
            $options['jsWidget'] = 'custom_pages.template.TemplateContainer';
            return $this->wrap('div', $result, $options, ['data-template-multiple' => $this->definition->allow_multiple]);
        } else {
            return $result;
        }
    }

    public function renderEmpty($options = [])
    {
        $options['jsWidget'] = 'custom_pages.template.TemplateContainer';
        return $this->renderEmptyDiv(Yii::t('CustomPagesModule.models_Container', 'Empty <br />Container'), $options, [
            'class' => 'emptyContainerBlock',
            'data-template-multiple' => $this->definition->allow_multiple
         ]);
    }

    public function addContainerItem($templateId, $index = null)
    {
        $index = ($index == null) ? $this->getNextIndex() : $index;

        ContainerContentItem::incrementIndex($this->id, $index);

        $item = new ContainerContentItem();
        $item->template_id = $templateId;
        $item->container_content_id = $this->id;
        $item->sort_order = $index;
        $item->save();

        return $item;
    }

    public function moveItem($itemId, $step)
    {
        $item = ContainerContentItem::findOne(['id' => $itemId]);

        if ($item == null || $item->container_content_id != $this->id) {
            return;
        }

        $nextIndex = $this->getNextIndex();

        // If move up and item is not last
        if ($step > 0 && $item->sort_order != $nextIndex - 1) {
            $oldIndex = $item->sort_order;
            $newIndex = $oldIndex + $step;
            $item->sort_order = ($newIndex < $nextIndex) ? $newIndex : ($nextIndex - 1);

            ContainerContentItem::decrementBetween($this->id, $oldIndex, $item->sort_order);

            $item->save();
        } else if ($step < 0 && $item->sort_order != 0) {
            $oldIndex = $item->sort_order;
            $newIndex = $oldIndex + $step;
            $item->sort_order = ($newIndex > 0) ? $newIndex : 0;

            ContainerContentItem::incrementBetween($this->id, $item->sort_order, $oldIndex);

            $item->save();
        }
    }

    public function createEmptyItem($templateId, $index = null)
    {
        $index = ($index == null) ? $this->getNextIndex() : $index;

        $item = new ContainerContentItem();
        $item->template_id = $templateId;
        $item->container_content_id = $this->id;
        $item->sort_order = $index;
        return $item;
    }

    public function getNextIndex()
    {
        return $this->getItems()->count();
    }

    public function hasItems()
    {
        return $this->getItems()->count() > 0;
    }

    public function getItems()
    {
        return $this->hasMany(ContainerContentItem::class, ['container_content_id' => 'id'])->orderBy('sort_order ASC');
    }

    public function canAddItem()
    {
        return $this->definition == null || $this->definition->allow_multiple || !$this->hasItems();
    }

    public function getTemplates()
    {
        return $this->definition->templates;
    }

    public function isSingleAllowedTemplate()
    {
        return count($this->templates) === 1;
    }

    public function renderForm($form)
    {
        return TemplateContentFormFields::widget([
                    'type' => 'container',
                    'form' => $form,
                    'model' => $this
        ]);
    }

}
