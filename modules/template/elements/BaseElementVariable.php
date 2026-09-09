<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\helpers\Html;
use humhub\modules\custom_pages\modules\template\services\TemplateInstanceRendererService;

class BaseElementVariable implements \Stringable
{
    public int $elementContentId;

    public bool $empty;

    public function __construct(protected BaseElementContent $elementContent)
    {
        $this->elementContentId = $this->elementContent->id ?? 0;
        $this->empty = $this->elementContent->isEmpty();
    }

    public static function instance(BaseElementContent $elementContent): static
    {
        return new static($elementContent);
    }

    public function __toString(): string
    {
        $output = $this->renderOutput();

        return $this->isInlineEditable() ? $this->renderInlineEditBlock($output) : $output;
    }

    /**
     * Renders the output of the element, overwritten by variables with a special output
     */
    protected function renderOutput(): string
    {
        return (string) strval($this->elementContent);
    }

    protected function isInlineEditable(): bool
    {
        return TemplateInstanceRendererService::inEditMode()
            && $this->elementContent->element?->isInlineEditingEnabled();
    }

    /**
     * Marks the output on the page for the inline editing, see humhub.custom_pages.template.editor.js
     */
    protected function renderInlineEditBlock(string $output): string
    {
        $attributes = ['data-editor-element-id' => $this->elementContent->element_id];
        $output = trim($output);

        if ($output === '') {
            // Placeholder, otherwise an empty element could not be found and edited on the page
            return Html::tag('span', Html::encode($this->elementContent->element->getTitle()), $attributes + ['class' => 'cp-editor-element-empty']);
        }

        if (preg_match('#^<([a-z][a-z0-9]*)[\s/>]#i', $output, $match) && $this->hasSingleRootElement($output)) {
            // Set the marker on the root tag of the output, e.g. <img ...> or <div>...</div>
            return '<' . $match[1] . Html::renderTagAttributes($attributes) . substr($output, strlen($match[1]) + 1);
        }

        // Text or several root tags: block content is wrapped into a div, inline content into a span
        $tag = preg_match('#<(p|div|ul|ol|table|h[1-6]|blockquote|pre|section|article|figure|form|hr)\b#i', $output) ? 'div' : 'span';

        return Html::tag($tag, $output, $attributes);
    }

    private function hasSingleRootElement(string $html): bool
    {
        $document = new \DOMDocument();
        $useInternalErrors = libxml_use_internal_errors(true);
        $loaded = $document->loadHTML('<?xml encoding="UTF-8"><div>' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($useInternalErrors);

        if (!$loaded || $document->documentElement === null) {
            return false;
        }

        $elements = 0;
        foreach ($document->documentElement->childNodes as $node) {
            if ($node->nodeType === XML_ELEMENT_NODE) {
                $elements++;
            } elseif ($node->nodeType === XML_TEXT_NODE && trim($node->textContent) !== '') {
                return false;
            }
        }

        return $elements === 1;
    }
}
