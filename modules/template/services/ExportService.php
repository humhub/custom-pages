<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\services;

use humhub\modules\custom_pages\modules\template\elements\BaseTemplateElementContent;
use humhub\modules\custom_pages\modules\template\elements\BaseTemplateElementContentDefinition;
use humhub\modules\custom_pages\modules\template\models\OwnerContent;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\models\TemplateContentOwner;
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

        $this->data['elements'] = [];
        foreach ($this->template->elements as $e => $element) {
            $this->data['elements'][$e] = $element->attributes;

            $defaultContent = $element->getDefaultContent();
            if ($defaultContent instanceof OwnerContent) {
                $this->data['elements'][$e]['ownerContent'] = $defaultContent->attributes;

                $ownerObject = $defaultContent->getOwner();
                if ($ownerObject instanceof TemplateContentOwner) {
                    $this->data['elements'][$e]['ownerContent']['ownerObject'] = $this->template->equals($ownerObject)
                        ? '#parentTemplate'
                        : $ownerObject->attributes;
                }

                $contentObject = $defaultContent->getInstance();
                if ($contentObject instanceof BaseTemplateElementContent) {
                    $contentData = $contentObject->attributes;

                    if ($contentObject->hasDefinition()) {
                        $definition = $contentObject->getDefinition();
                        if ($definition instanceof BaseTemplateElementContentDefinition) {
                            $contentData['definitionClass'] = get_class($definition);
                            $contentData['definitionObject'] = $definition->attributes;
                        }
                    }

                    // Attach files
                    $files = [];
                    foreach ($contentObject->fileManager->find()->each() as $f => $file) {
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

                    $this->data['elements'][$e]['ownerContent']['contentObject'] = $contentData;
                }
            }
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
