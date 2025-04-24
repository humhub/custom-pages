<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\models\forms;

use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;

/**
 * Description of UserGroupForm
 *
 * @author buddha
 */
class EditMultipleElementsForm extends \yii\base\Model
{
    public $isNewRecord = false;
    public $editDefault = true;
    public $owner;
    public ?Template $template = null;

    /**
     * @var ContentFormItem[]
     */
    public array $contentMap = [];
    public $scenario = 'edit';

    public function setOwnerTemplateId($templateId)
    {
        $this->setTemplate($templateId);
        $this->owner = $this->template;
    }

    // Todo: is the templateId even needed since the woner should have contain a templateid ...
    public function setOwner($ownerModel, $ownerId, $templateId = null)
    {
        if (!is_string($ownerModel)) {
            $templateId = $ownerId;
            $this->owner = $ownerModel;
        } else {
            $this->owner = call_user_func($ownerModel . "::findOne", ['id' => $ownerId]);
        }

        $this->setTemplate($templateId);
    }

    protected function setTemplate($templateId)
    {
        $this->template = Template::find()->where(['custom_pages_template.id' => $templateId])->joinWith('elements')->one();
        $this->prepareContentInstances();
    }

    public function prepareContentInstances()
    {
        $templateInstance = $this->owner instanceof TemplateInstance ? $this->owner : null;
        $elementContents = $this->template->getElementContents($templateInstance);

        foreach ($elementContents as $elementContent) {
            $contentItem = new ContentFormItem([
                'elementContent' => $elementContent,
                'element' => $elementContent->element,
                'editDefault' => $this->editDefault,
                'scenario' => $this->scenario]);
            $this->contentMap[$contentItem->key] = $contentItem;
        }
    }

    public function getElement($name)
    {
        foreach ($this->template->elements as $element) {
            if ($name === $element->name) {
                return $element;
            }
        }
    }

    public function load($data, $formName = null)
    {
        if (!$this->template->canEdit()) {
            return false;
        }

        // This prevents items without elements from being rejected
        if (parent::load($data) && empty($this->contentMap)) {
            return true;
        }

        $result = false;

        // If one of the content was loaded we expect a successful form submit
        foreach ($this->contentMap as $contentItem) {
            if ($contentItem->load($data)) {
                $result = true;
            }
        }

        return $result;
    }

    public function validate($attributeNames = null, $clearErrors = true)
    {
        // Content is not mandatory from administration side
        if ($this->scenario === 'edit-admin') {
            return true;
        }

        $result = true;

        // If one of the content is not valid we cannot submit a form completely
        foreach ($this->contentMap as $contentItem) {
            if (!$contentItem->validate($attributeNames, $clearErrors)) {
                $result = false;
            }
        }

        return $result;
    }

    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $transaction = Template::getDb()->beginTransaction();

        try {
            foreach ($this->contentMap as $contentItem) {
                $contentItem->save($this->owner);
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return true;
    }
}
