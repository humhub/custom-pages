<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\widgets;

use humhub\modules\custom_pages\helpers\PageType;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\modules\template\assets\InlineEditorAsset;
use humhub\modules\custom_pages\modules\template\assets\TemplatePageStyleAsset;
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
     * @var bool defines if this page can be edited by the current user
     */
    public $canEdit;

    /**
     * @var string defines what mode is active: 'edit'
     */
    public $mode;

    /**
     * @var CustomPage page instance
     */
    public $page;

    /**
     * @var \humhub\modules\content\components\ContentContainerActiveRecord
     */
    public $contentContainer;

    /**
     * fast
     */
    //public $fadeIn = 'slow';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->init = $this->canEdit && $this->mode === 'edit';
        ob_start();
        ob_implicit_flush(false);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        TemplatePageStyleAsset::register($this->getView());

        if ($this->canEdit && $this->mode === 'edit') {
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
        if ($this->page instanceof CustomPage && !$this->contentContainer && $this->page->getTargetId() !== PageType::TARGET_ACCOUNT_MENU) {
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
