<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\services;

use humhub\modules\custom_pages\modules\template\elements\BaseElementContent;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\file\models\File;
use Yii;
use yii\web\Response;

class ExportService
{
    private Template $template;
    private ?array $data = null;

    public function __construct(Template $template)
    {
        $this->template = $template;
    }

    public static function instance(Template $template): self
    {
        return new self($template);
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function export(): self
    {
        $this->data = $this->template->attributes;
        unset($this->data['id']);
        unset($this->data['created_at']);
        unset($this->data['created_by']);
        unset($this->data['updated_at']);
        unset($this->data['updated_by']);

        $this->data['elements'] = [];
        foreach ($this->template->elements as $e => $element) {
            $this->data['elements'][$e] = $element->attributes;
            unset($this->data['elements'][$e]['id']);
            unset($this->data['elements'][$e]['template_id']);

            if ($elementContent = $element->getDefaultContent()) {
                $this->data['elements'][$e]['elementContent'] = self::getElementContentData($elementContent);
            }
        }

        return $this;
    }

    public static function getElementContentData(BaseElementContent $elementContent): array
    {
        $data = $elementContent->attributes;
        unset($data['id']);
        unset($data['element_id']);
        unset($data['template_instance_id']);

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
            $data['attachedFiles'] = $files;
        }

        return $data;
    }

    private function getFileName(): string
    {
        return Template::getTypeTitle($this->template->type) . '_' . $this->template->name . '_' . date('Y-m-d_H-i') . '.json';
    }

    public function send(): Response
    {
        return Yii::$app->response->sendContentAsFile(json_encode($this->getData()), $this->getFileName());
    }
}
