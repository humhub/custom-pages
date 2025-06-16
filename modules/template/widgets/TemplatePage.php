<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\widgets;

use humhub\modules\content\helpers\ContentContainerHelper;
use humhub\modules\custom_pages\helpers\PageType;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\modules\template\assets\InlineEditorAsset;
use humhub\modules\custom_pages\modules\template\assets\TemplatePageStyleAsset;
use humhub\modules\custom_pages\modules\template\services\TemplateInstanceRendererService;
use humhub\widgets\JsWidget;
use yii\helpers\Html;

/**
 * Description of TemplatePage
 *
 * @author buddha
 */
class TemplatePage extends JsWidget
{
    /**
     * @inheritdoc
     */
    public $id = "templatePageRoot";

    /**
     * @inheritdoc
     */
    public $jsWidget = 'custom_pages.template.editor.TemplateInlineEditor';

    /**
     * @inheritdoc
     */
    public $init = true;

    /**
     * @var CustomPage page instance
     */
    public $page;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->init = TemplateInstanceRendererService::inEditMode();
        ob_start();
        ob_implicit_flush(false);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        TemplatePageStyleAsset::register($this->getView());

        if (TemplateInstanceRendererService::inEditMode()) {
            InlineEditorAsset::register($this->getView());
        }

        return Html::tag('div', ob_get_clean(), $this->getOptions());
    }

    /**
     * @inheritdoc
     */
    public function getAttributes()
    {
        $cssClass = '';

        //TODO: fullscreen flag
        if ($this->page instanceof CustomPage &&
            ContentContainerHelper::getCurrent() === null &&
            $this->page->getTargetId() !== PageType::TARGET_ACCOUNT_MENU) {
            $cssClass .= 'container ';
        }

        $cssClass .= $this->page->hasAttribute('cssClass') && !empty($this->page->cssClass)
            ? $this->page->cssClass
            : 'custom-pages-page';

        return [
            'class' => $cssClass,
        ];
    }
}
