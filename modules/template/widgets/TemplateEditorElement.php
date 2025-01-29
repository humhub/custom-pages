<?php

namespace humhub\modules\custom_pages\modules\template\widgets;

use humhub\modules\custom_pages\modules\template\elements\BaseTemplateElementContent;

/**
 * Description of TemplatePage
 *
 * @author buddha
 */
class TemplateEditorElement extends \humhub\widgets\JsWidget
{
    /**
     * @inheritdoc
     */
    public $jsWidget = 'custom_pages.template.TemplateElement';

    /**
     * @var string additional style class of editor root
     */
    public $page;

    /**
     * @var BaseTemplateElementContent
     */
    public $templateContent;

    /**
     * @var array render options for this editor element.
     */
    public $renderOptions = [];

    /**
     * @var array render html attributes.
     */
    public $renderAttributes = [];

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->jsWidget = isset($this->renderOptions['jsWidget']) ? $this->renderOptions['jsWidget'] : $this->jsWidget;
        return parent::run();
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        return [
            'template-element-title' => $this->_getOption($this->renderOptions, 'element_title'),
            'template-element' => $this->_getOption($this->renderOptions, 'element_name'),
            'template-id' => $this->_getOption($this->renderOptions, 'template_id'),
            'template-instance-id' => $this->_getOption($this->renderOptions, 'template_instance_id'),
            'template-instance-type' => $this->_getOption($this->renderOptions, 'template_instance_type'),
            'template-element-id' => $this->_getOption($this->renderOptions, 'element_id'),
            'template-element-content-id' => $this->_getOption($this->renderOptions, 'element_content_id'),
            'template-default' => $this->_getOption($this->renderOptions, 'default', '0'),
            'template-empty' => $this->_getOption($this->renderOptions, 'empty', '0'),
            'template-label' => $this->templateContent->getLabel(),
            'template-content' => get_class($this->templateContent),
        ];
    }

    private function _getOption($options, $key, $default = null)
    {
        if (isset($options[$key])) {
            if (is_bool($options[$key])) {
                return ($options[$key]) ? '1' : '0';
            } else {
                return $options[$key];
            }
        } else {
            return $default;
        }
        return isset($options[$key]) ? strval($options[$key]) : $default;
    }

    /**
     * @inheritdoc
     */
    public function getAttributes()
    {
        if (isset($this->renderOptions['htmlOptions'])) {
            $this->renderAttributes = array_merge($this->renderAttributes, $this->renderOptions['htmlOptions']);
        }
        return $this->renderAttributes;
    }
}
