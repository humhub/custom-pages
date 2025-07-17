<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\libs\Html;
use humhub\modules\custom_pages\modules\template\services\TemplateInstanceRendererService;
use Yii;

class ContainerElementVariable extends BaseElementVariable
{
    public const EDIT_WRAPPER_ATTR_CACHE_KEY = self::class . 'editWrapperAttributes';

    /**
     * @var ContainerItem[]|null Cached items
     */
    private ?array $_items = null;

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

        if ($this->areEditWrapperAttributesRendered()) {
            return $content;
        }

        $tagName = preg_match('#<(tr).+?</\1>#is', $content) ? 'tbody' : 'div';

        return Html::tag($tagName, $content, $this->getEditWrapperAttributes());
    }

    protected function getEditWrapperAttributes(): array
    {
        $attributes = [
            'data-editor-container-id' => $this->elementContent->id,
        ];

        if ($this->getItems() === []) {
            $attributes['class'] = 'cp-editor-container-empty';
        }

        return $attributes;
    }

    public function editWrapperAttributes(): string
    {
        if (TemplateInstanceRendererService::inEditMode()) {
            $rendered = $this->getRenderedEditWrapperAttributes();
            $rendered[] = $this->elementContent->id;
            Yii::$app->runtimeCache->set(self::EDIT_WRAPPER_ATTR_CACHE_KEY, $rendered);

            return Html::renderTagAttributes($this->getEditWrapperAttributes());
        }

        return '';
    }

    private function areEditWrapperAttributesRendered(): bool
    {
        return in_array($this->elementContent->id, $this->getRenderedEditWrapperAttributes());
    }

    private function getRenderedEditWrapperAttributes(): array
    {
        $rendered = Yii::$app->runtimeCache->get(self::EDIT_WRAPPER_ATTR_CACHE_KEY);
        return is_array($rendered) ? $rendered : [];
    }
}
