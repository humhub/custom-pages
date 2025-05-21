<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\services;

use humhub\modules\custom_pages\Module;
use humhub\modules\custom_pages\modules\template\events\DefaultTemplateEvent;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\models\TemplateElement;
use Yii;
use yii\db\ActiveRecord;

/**
 * Service to import Template
 */
class ImportService extends BaseImportService
{
    public const EVENT_DEFAULT_TEMPLATES = 'defaultTemplates';

    private ?string $type = null;
    public ?Template $template = null;
    public bool $allowUpdateDefaultTemplates = false;

    public function __construct(?string $type = null)
    {
        $this->type = $type;

        if ($module = $this->getModule()) {
            $this->allowUpdateDefaultTemplates = $module->allowUpdateDefaultTemplates;
        }
    }

    public static function instance(?string $type = null): self
    {
        return new self($type);
    }

    public function importFromFolder(string $path): bool
    {
        if (!is_dir($path)) {
            $this->addError('Wrong default templates path "' . $path . '"!');
            return false;
        }

        $result = true;
        foreach (scandir($path) as $file) {
            if (str_ends_with($file, '.json')) {
                $result = $this->importFromFile($path . '/' . $file) && $result;
            }
        }

        return $result;
    }

    public function importFromFile(string $path): bool
    {
        if (!file_exists($path)) {
            $this->addError('The import file is not found!');
            return false;
        }

        try {
            $data = json_decode(file_get_contents($path), true);
        } catch (\Exception $e) {
            $this->addError('The import file is not readable! Error: ' . $e->getMessage());
            return false;
        }

        if (empty($data['name'])) {
            $this->addError(Yii::t('CustomPagesModule.template', 'Wrong import data!'));
            return false;
        }

        if (isset($data['type'], $this->type) && $data['type'] !== $this->type) {
            $this->addError(Yii::t('CustomPagesModule.template', 'The template can be imported only as {type}!', [
                'type' => Template::getTypeTitle($data['type']),
            ]));
            return false;
        }

        return $this->run($data);
    }

    /**
     * @inheritdoc
     */
    public function run(array $data): bool
    {
        if (!$this->importTemplate($data)) {
            return false;
        }

        $importedElementNames = [];
        if (isset($data['elements']) && is_array($data['elements'])) {
            foreach ($data['elements'] as $element) {
                if ($element = $this->importElement($element)) {
                    $importedElementNames[] = $element->name;
                }
            }
        }

        // Delete old template elements that are not found in the new template
        $oldElements = TemplateElement::find()
            ->where(['template_id' => $this->template->id])
            ->andWhere(['NOT IN', 'name', $importedElementNames]);
        foreach ($oldElements->each() as $oldElement) {
            $oldElement->delete();
        }

        return !$this->hasErrors();
    }

    private function importTemplate(array $data): bool
    {
        $template = Template::findOne(['name' => $data['name']]) ?? new Template();

        if ($template->is_default && !$template->isNewRecord) {
            // Check if default templates can be updated
            if (!$this->allowUpdateDefaultTemplates) {
                $this->addError(Yii::t('CustomPagesModule.template', 'Cannot import default template!'));
                return false;
            }

            // If the default template was modified
            if ($template->updated_at !== null || $template->updated_by !== null) {
                // Rename the modified default template
                $uniqueName = $template->name . ' (Modified)';
                $uniqueIndex = 1;
                while (Template::findOne(['name' => $uniqueName])) {
                    $uniqueName = $template->name . ' (Modified ' . ++$uniqueIndex . ')';
                }
                $template->name = $uniqueName;
                $template->save();

                // Create new default template
                $template = new Template();
            }
        }

        $template->type = $data['type'];
        $template->name = $data['name'];
        $template->engine = $data['engine'] ?? 'twig';
        $template->description = $data['description'] ?? '';
        $template->source = $data['source'] ?? '';
        $template->allow_for_spaces = $data['allow_for_spaces'] ?? false;
        $template->is_default = $data['is_default'] ?? false;

        $this->template = $this->saveRecord($template);

        return $this->template instanceof Template;
    }

    private function importElement(array $data): ?TemplateElement
    {
        $element = TemplateElement::findOne([
            'template_id' => $this->template->id,
            'name' => $data['name'],
        ]);

        if ($element) {
            $element->setScenario(TemplateElement::SCENARIO_EDIT_ADMIN);
        } else {
            $element = new TemplateElement();
            $element->setScenario(TemplateElement::SCENARIO_CREATE);
        }

        $element->template_id = $this->template->id;
        $element->name = $data['name'] ?? '';
        $element->content_type = $data['content_type'] ?? '';
        $element->title = $data['title'] ?? '';
        $element->dyn_attributes = $data['dyn_attributes'] ?? '';
        if (is_array($element->dyn_attributes)) {
            $element->dyn_attributes = json_encode($element->dyn_attributes);
        }

        if (!class_exists($element->content_type)) {
            $this->addError('Element content class "' . $element->content_type . '" does not exist!');
            return null;
        }

        if (!$this->saveRecord($element)) {
            return null;
        }

        if (isset($data['elementContent'])) {
            $data['elementContent']['element_id'] = $element->id;
            $this->importElementContent($element, $data['elementContent']);
        }

        return $element;
    }

    private function importElementContent(TemplateElement $element, array $data): ?ActiveRecord
    {
        $elementContent = $element->getDefaultContent(true);

        foreach ($data as $name => $value) {
            if ($name === 'id' ||
                ($name !== 'dyn_attributes' && is_array($value)) ||
                !$elementContent->hasAttribute($name)) {
                continue;
            }
            $elementContent->$name = $value;
        }

        $elementContent = $this->saveRecord($elementContent);
        if (!$elementContent) {
            return null;
        }

        if (isset($data['attachedFiles']) && is_array($data['attachedFiles'])) {
            $this->attachFiles($elementContent, $data['attachedFiles']);
        }

        return $elementContent;
    }

    public function importDefaultTemplates(): bool
    {
        if (!$this->getModule()) {
            // Check because it may be called from other external module
            return true;
        }

        $event = new DefaultTemplateEvent();
        $event->addPath('@custom_pages/resources/templates');
        DefaultTemplateEvent::trigger($this, self::EVENT_DEFAULT_TEMPLATES, $event);

        $this->allowUpdateDefaultTemplates = true;

        $result = true;
        foreach ($event->getPaths() as $path) {
            $result = $this->importFromFolder(Yii::getAlias($path)) && $result;
        }

        return $result;
    }

    private function getModule(): ?Module
    {
        $module = Yii::$app->getModule('custom_pages');
        return $module instanceof Module && $module->isEnabled ? $module : null;
    }
}
