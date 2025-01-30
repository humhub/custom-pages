<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\services;

use humhub\modules\custom_pages\modules\template\elements\BaseTemplateElementDefinition;
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

    public function export(): self
    {
        $this->data = $this->template->attributes;
        unset($this->data['id']);

        $this->data['elements'] = [];
        foreach ($this->template->elements as $e => $element) {
            $this->data['elements'][$e] = $element->attributes;
            unset($this->data['elements'][$e]['id']);
            unset($this->data['elements'][$e]['template_id']);

            $elementContent = $element->getDefaultContent();

            $contentData = $elementContent->attributes;
            unset($contentData['id']);
            unset($contentData['element_id']);
            unset($contentData['template_instance_id']);

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
                $contentData['attachedFiles'] = $files;
            }

            $this->data['elements'][$e]['elementContent'] = $contentData;
        }

        return $this;
    }

    private function getFileName(): string
    {
        return Template::getTypeTitle($this->template->type) . '_' . $this->template->name . '_' . date('Y-m-d_H-i') . '.json';
    }

    public function send(): Response
    {
        return Yii::$app->response->sendContentAsFile(json_encode($this->data), $this->getFileName());
    }
}
