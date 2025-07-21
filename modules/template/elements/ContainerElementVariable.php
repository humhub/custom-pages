<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\libs\Html;
use humhub\modules\custom_pages\lib\templates\twig\TwigEngine;
use humhub\modules\custom_pages\modules\template\services\TemplateInstanceRendererService;
use Yii;

/**
 * @property-read string $editWrapperAttributes
 */
class ContainerElementVariable extends BaseElementVariable
{
    /**
     * @var ContainerItem[]|null Cached items
     */
    private ?array $_items = null;

    /**
     * @var bool Set true to don't render the container wrapper twice on edit mode when attributes are
     *           already rendered in the wrapper tag like `<div {{ container.editWrapperAttributes }}>`
     */
    private bool $isEditWrapperRendered = false;

    public function __construct(BaseElementContent $elementContent)
    {
        parent::__construct($elementContent);
        TwigEngine::registerSandboxExtensionAllowedFunctions(static::class, [
            'getEditWrapperAttributes',
        ]);
    }

    public function __toString()
    {
        try {
            if (!$this->elementContent->isEmpty()) {
                return $this->render();
            }
        } catch (\Exception $e) {
            return strval($e);
        }

        return '';
    }

    private function getItems(): array
    {
        if ($this->_items === null) {
            $this->_items = $this->elementContent->items;
        }

        return $this->_items;
    }

    private function render(): string
    {
        $result = '';
        foreach ($this->getItems() as $containerItem) {
            $result .= $containerItem->render();
        }

        if (TemplateInstanceRendererService::inEditMode()) {
            return $this->renderEditBlock($result);
        }

        return $result;
    }

    /**
     * Render block for inline editing
     *
     * @param string $content
     * @return string
     */
    protected function renderEditBlock(string $content): string
    {
        if ($this->getItems() === []) {
            $content = Html::tag('div', Yii::t('CustomPagesModule.model', 'Empty <br />Container'));
        }

        if ($this->isEditWrapperRendered) {
            return $content;
        }

        $tagName = preg_match('#<(tr).+?</\1>#is', $content) ? 'tbody' : 'div';

        return Html::tag($tagName, $content, $this->getEditWrapperAttributesArray());
    }

    protected function getEditWrapperAttributesArray(): array
    {
        $attributes = ['data-editor-container-id' => $this->elementContent->id];

        if ($this->getItems() === []) {
            $attributes['data-editor-container-empty'] = true;
        }

        return $attributes;
    }

    /**
     * It is used to render Twig property {{ container.editWrapperAttributes }}
     *
     * @return string
     */
    public function getEditWrapperAttributes(): string
    {
        if (TemplateInstanceRendererService::inEditMode()) {
            $this->isEditWrapperRendered = true;
            return Html::renderTagAttributes($this->getEditWrapperAttributesArray());
        }

        return '';
    }
}
