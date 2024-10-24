<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\services;

use humhub\modules\custom_pages\modules\template\models\OwnerContent;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\models\TemplateContentActiveRecord;
use humhub\modules\custom_pages\modules\template\models\TemplateElement;
use yii\db\ActiveRecord;
use Yii;

class ImportService
{
    private string $type;
    private string $filePath;
    private array $errors = [];
    public ?Template $template = null;

    public function __construct(string $type, string $filePath)
    {
        $this->type = $type;
        $this->filePath = $filePath;
    }

    public function addError(string $error): void
    {
        $this->errors[] = $error;
    }

    public function hasErrors(): bool
    {
        return $this->getErrors() !== [];
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function run(): bool
    {
        if (!file_exists($this->filePath)) {
            $this->addError('The import file is not found!');
            return false;
        }

        try {
            $data = json_decode(file_get_contents($this->filePath), true);
        } catch (\Exception $e) {
            $this->addError('The import file is not readable! Error: ' . $e->getMessage());
            return false;
        }

        if (empty($data['name'])) {
            $this->addError(Yii::t('CustomPagesModule.template', 'Wrong import data!'));
            return false;
        }

        if (!$this->importTemplate($data)) {
            return false;
        }

        if (isset($data['elements']) && is_array($data['elements'])) {
            foreach ($data['elements'] as $element) {
                $this->importElement($element);
            }
        }

        return !$this->hasErrors();
    }

    private function saveRecord(ActiveRecord $record): ?ActiveRecord
    {
        if ($record->validate() && $record->save()) {
            return $record;
        }

        $this->addError(implode(' ', $record->getErrorSummary(true)));
        return null;
    }

    private function importTemplate(array $data): bool
    {
        $template = Template::findOne(['name' => $data['name']]);
        if ($template instanceof Template) {
            // Delete old template and its linked elements before importing a new one with the same name
            if (!$template->delete()) {
                $this->addError('Cannot delete the old template "' . $data['name'] . '"!');
                return false;
            }
        }

        $template = new Template();
        $template->type = $this->type;
        $template->name = $data['name'];
        $template->engine = $data['engine'] ?? 'twig';
        $template->description = $data['description'] ?? '';
        $template->source = $data['source'] ?? '';
        $template->allow_for_spaces = $data['allow_for_spaces'] ?? false;
        $template->allow_inline_activation = $data['allow_inline_activation'] ?? false;

        $this->template = $this->saveRecord($template);

        return $this->template instanceof Template;
    }

    public function importElement(array $data): ?TemplateElement
    {
        $element = new TemplateElement();
        $element->setScenario(TemplateElement::SCENARIO_CREATE);
        $element->template_id = $this->template->id;
        $element->name = $data['name'] ?? '';
        $element->content_type = $data['content_type'] ?? '';
        $element->title = $data['title'] ?? '';

        if (!$this->saveRecord($element)) {
            return null;
        }

        if (isset($data['templateContent'])) {
            if ($templateContent = $this->importTemplateContent($element, $data['templateContent'])) {
                $this->importOwnerContent($element, $templateContent, $data['ownerContent']);
            }
        }

        return $element;
    }

    public function importTemplateContent(TemplateElement $element, array $data): ?TemplateContentActiveRecord
    {
        if (!class_exists($element->content_type)) {
            $this->addError('Wrong template element content type "' . $element->content_type . '"!');
            return null;
        }

        /* @var TemplateContentActiveRecord $templateContent */
        $templateContent = Yii::createObject($element->content_type);
        foreach ($data as $key => $value) {
            if ($key === 'id') {
                continue;
            }
            $templateContent->$key = $value;
        }

        return $this->saveRecord($templateContent);
    }

    public function importOwnerContent(TemplateElement $element, TemplateContentActiveRecord $templateContent, array $data): ?OwnerContent
    {
        $ownerContent = new OwnerContent();
        $ownerContent->element_name = $data['element_name'];
        $ownerContent->owner_model = $data['owner_model']; // TODO: Create new object instead of getting from the data
        $ownerContent->owner_id = $element->template_id;
        $ownerContent->content_type = $templateContent::class;
        $ownerContent->content_id = $templateContent->id;
        $ownerContent->use_default = $data['use_default'];

        return $this->saveRecord($ownerContent);
    }
}
