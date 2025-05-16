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
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use Yii;
use yii\web\Response;

class ExportInstanceService
{
    private TemplateInstance $instance;
    private ?array $data = null;

    public function __construct(TemplateInstance $instance)
    {
        $this->instance = $instance;
    }

    public static function instance(TemplateInstance $instance): self
    {
        return new self($instance);
    }

    public function export(): self
    {
        return $this
            ->exportInstance()
            ->exportPage()
            ->exportTemplate()
            ->exportContainer();
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
        $this->data = $this->instance->attributes;
        unset($this->data['id']);
        unset($this->data['template_id']);
        unset($this->data['page_id']);
        unset($this->data['container_item_id']);
        return $this;
    }

    private function exportTemplate(): self
    {
        $template = $this->instance->template;
        $this->data['template'] = $template->name;
        $this->data['templates'][$template->name] = ExportService::instance($template)->export()->getData();
        return $this;
    }

    private function exportPage(): self
    {
        if ($this->instance->isPage()) {
            $this->data['page'] = $this->instance->page->attributes;
            unset($this->data['page']['id']);
            unset($this->data['page']['type']);
        }

        return $this;
    }

    private function exportContainer(): self
    {
        if ($this->instance->isContainer()) {
            $this->data['containerItem'] = $this->getContainerItemData($this->instance->containerItem);
        } else {
            $this->data['contents'] = $this->getContentsData($this->instance);
        }

        return $this;
    }

    private function getContentsData(TemplateInstance $templateInstance): array
    {
        $elementContents = BaseElementContent::find()->where(['template_instance_id' => $templateInstance->id]);

        $data = [];
        foreach ($elementContents->each() as $elementContent) {
            $contentData = ExportService::getElementContentData($elementContent);
            if ($elementContent instanceof ContainerElement) {
                $contentData['containerItems'] = [];
                foreach ($elementContent->items as $containerItem) {
                    $contentData['containerItems'][] = $this->getContainerItemData($containerItem);
                }
            }
            $data[$elementContent->element->name] = $contentData;
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
        if (!isset($this->data['templates'][$template->name])) {
            $this->data['templates'][$template->name] = ExportService::instance($template)->export()->getData();
        }

        $data['contents'] = $this->getContentsData($containerItem->templateInstance);

        return $data;
    }
}
