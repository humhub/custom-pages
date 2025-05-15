<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\services;

use humhub\modules\custom_pages\modules\template\elements\BaseElementContent;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use humhub\modules\file\models\File;
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
            ->exportContents();
    }

    private function getFileName(): string
    {
        return $this->instance->getType() . '_' . $this->instance->template->name . '_' . date('Y-m-d_H-i') . '.json';
    }

    public function send(): Response
    {
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
        $this->data['template'] = ExportService::instance($this->instance->template)->export()->getData();
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

    private function exportContents(): self
    {
        $elementContents = BaseElementContent::find()->where(['template_instance_id' => $this->instance->id]);
        if (!$elementContents->exists()) {
            return $this;
        }

        $this->data['contents'] = [];
        foreach ($elementContents->each() as $elementContent) {
            $content = $elementContent->attributes;
            unset($content['id']);
            unset($content['element_id']);
            unset($content['template_instance_id']);

            // Attach files
            $files = [];
            foreach ($elementContent->fileManager->find()->each() as $f => $file) {
                /* @var File $file */
                if ($file->store->has()) {
                    foreach ($file->attributes() as $attribute) {
                        if ($attribute !== 'id' && $attribute !== 'metadata') {
                            $files[$f][$attribute] = $file->$attribute;
                        }
                    }
                    $files[$f]['base64Content'] = base64_encode(file_get_contents($file->store->get()));
                }
            }
            if ($files !== []) {
                $content['attachedFiles'] = $files;
            }

            $this->data['contents'][$elementContent->element->name] = $content;
        }

        return $this;
    }
}
