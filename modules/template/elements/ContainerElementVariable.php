<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\helpers\Html;
use humhub\modules\custom_pages\modules\template\services\TemplateInstanceRendererService;
use Yii;

class ContainerElementVariable extends BaseElementVariable
{
    public function __toString()
    {
        // Note that the editMode can be set to $this->options in this case
        $options = [];

        if (TemplateInstanceRendererService::inEditMode()) {
            $options = array_merge([
                'element_id' => $this->elementContent->element_id,
                'element_content_id' => $this->elementContent->id,
                'element_name' => $this->elementContent->element->name,
                'element_title' => $this->elementContent->element->getTitle(),
                'empty' => $this->elementContent->isEmpty(),
                'default' => $this->elementContent->isDefault(),
            ], $options);
        }

        try {
            if (!$this->elementContent->isEmpty()) {
                return $this->render2($options);
            }
        } catch (\Exception $e) {
            return strval($e);
        }

        return '';
    }

    private function render2($options = [])
    {
        $items = $this->elementContent->items;

        if (empty($items)) {
            if (TemplateInstanceRendererService::inEditMode()) {
                $content = Html::tag('div', Yii::t('CustomPagesModule.model', 'Empty <br />Container'));
                return $this->renderEditBlock($content, ['class' => 'cp-editor-container-empty']);
            }
            return '';
        }

        $result = '';
        foreach ($items as $containerItem) {
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
    protected function renderEditBlock(string $content, array $options = []): string
    {
        if (preg_match('#<(tr).+?</\1>#is', $content)) {
            $tagName = 'tbody';
        } else {
            $tagName = 'div';
        }

        return Html::tag($tagName, $content, array_merge([
            'data-editor-container-id' => $this->elementContent->id,
        ], $options));
    }
}
