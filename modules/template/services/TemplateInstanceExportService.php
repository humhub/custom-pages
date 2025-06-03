<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\services;

use humhub\modules\custom_pages\modules\template\elements\BaseElementContent;
use humhub\modules\custom_pages\modules\template\elements\ContainerElement;
use humhub\modules\custom_pages\modules\template\elements\ContainerItem;
use humhub\modules\custom_pages\modules\template\models\TemplateElement;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use Yii;
use yii\web\Response;

class TemplateInstanceExportService
{
    /**
     * Version for exporting JSON files
     * NOTE: Update it when JSON structure is changed, to avoid errors on import
     */
    public const VERSION = '1.0';

    private TemplateInstance $instance;
    private ?TemplateElement $element = null;
    private ?array $data = null;

    public function __construct(TemplateInstance $instance, ?TemplateElement $element = null)
    {
        $this->instance = $instance;
        $this->element = $element;
    }

    public static function instance(TemplateInstance $instance, ?TemplateElement $element = null): self
    {
        return new self($instance, $element);
    }

    public function export(): self
    {
        $this->data = ['version' => self::VERSION];

        if ($this->element instanceof TemplateElement) {
            return $this->exportElementItems();
        }

        return $this
            ->exportInstance()
            ->exportTemplate()
            ->exportElements();
    }

    private function getFileName(): string
    {
        return $this->instance->getType() . '_' . $this->instance->template->name . '_' . date('Y-m-d_H-i') . '.json';
    }

    public function send(): Response
    {
        return Yii::$app->controller->asJson($this->data);
        return Yii::$app->response->sendContentAsFile(json_encode($this->data), $this->getFileName());
    }

    private function exportInstance(): self
    {
        $this->data += $this->instance->attributes;
        unset($this->data['id']);
        unset($this->data['template_id']);
        unset($this->data['page_id']);
        unset($this->data['container_item_id']);

        return $this;
    }

    private function exportTemplate(): self
    {
        $template = $this->instance->template;
        if ($this->instance->isContainer()) {
            $containerItem = $this->instance->containerItem;
            $this->data['sort_order'] = $containerItem->sort_order ?? 0;
            $this->data['title'] = $containerItem->title ?? '';
        }
        $this->data['template'] = $template->name;
        return $this;
    }

    private function exportElements(): self
    {
        $templateInstance = $this->instance;

        if ($templateInstance->isContainer()) {
            $templateInstance = $templateInstance->containerItem->templateInstance;
        }

        $this->data['elements'] = $this->getElementsData($templateInstance);

        return $this;
    }

    private function exportElementItems(): self
    {
        $elementContent = BaseElementContent::find()
            ->where(['template_instance_id' => $this->instance->id])
            ->andWhere(['element_id' => $this->element->id])
            ->one();

        $this->data += $this->getContainerItems($elementContent);

        return $this;
    }

    private function getElementsData(TemplateInstance $templateInstance): array
    {
        $elementContents = BaseElementContent::find()->where(['template_instance_id' => $templateInstance->id]);

        $data = [];
        foreach ($elementContents->each() as $elementContent) {
            /* @var BaseElementContent $elementContent */
            $contentData = ['__element_type' => get_class($elementContent)];
            $contentData += $elementContent->dyn_attributes;
            $contentData += $this->getContainerItems($elementContent);

            $files = TemplateExportService::getElementContentFiles($elementContent);
            if ($files !== []) {
                $contentData['__element_files'] = $files;
            }

            $data[$elementContent->element->name] = $contentData;
        }

        return $data;
    }

    private function getContainerItems(?BaseElementContent $elementContent): array
    {
        $data = [];

        if ($elementContent instanceof ContainerElement) {
            $data['__element_items'] = [];
            foreach ($elementContent->items as $containerItem) {
                $data['__element_items'][] = $this->getContainerItemData($containerItem);
            }
        }

        return $data;
    }

    private function getContainerItemData(ContainerItem $containerItem): array
    {
        $data = $containerItem->attributes;
        unset($data['id']);
        unset($data['element_content_id']);

        $template = $containerItem->template;
        $data['template'] = $template->name;
        $data['elements'] = $this->getElementsData($containerItem->templateInstance);

        return $data;
    }
}
