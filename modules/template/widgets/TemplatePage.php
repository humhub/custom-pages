<?php

namespace humhub\modules\custom_pages\modules\template\widgets;

use Yii;
use humhub\modules\custom_pages\models\Page;

/**
 * Description of TemplatePage
 *
 * @author buddha
 */
class TemplatePage extends \humhub\widgets\JsWidget
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
     * @var boolean defines if this page can be edited by the current user
     */
    public $canEdit;

    /**
     * @var boolean defines the editmode is active
     */
    public $editMode;

    /**
     * @var \humhub\modules\custom_pages\models\CustomContentContainer page instance
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
        $this->init = $this->canEdit && $this->editMode;
        ob_start();
        ob_implicit_flush(false);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        \humhub\modules\custom_pages\modules\template\assets\TemplatePageStyleAsset::register($this->getView());
        
        if ($this->canEdit && $this->editMode) {
            \humhub\modules\custom_pages\modules\template\assets\InlineEditorAsset::register($this->getView());

            $this->getView()->registerJsConfig('custom_pages.template.editor', [
                'text' => [
                    'toggleOnText' => Yii::t('CustomPagesModule.base', 'On'),
                    'toggleOffText' => Yii::t('CustomPagesModule.base', 'Off'),
                    'editTemplateText' => Yii::t('CustomPagesModule.views_view_template', 'Edit Template'),
                    'confirmDeleteButton' => Yii::t('CustomPagesModule.base', 'Delete'),
                    'confirmDeleteContentHeader' => Yii::t('CustomPagesModule.modules_template_controller_OwnerContentController', '<strong>Confirm</strong> content deletion'),
                    'confirmDeleteContentBody' => Yii::t('CustomPagesModule.modules_template_widgets_views_confirmDeletionModal', 'Do you really want to delete this content?'),
                    'confirmDeleteElementHeader' => Yii::t('CustomPagesModule.modules_template_controller_OwnerContentController', '<strong>Confirm</strong> element deletion'),
                    'confirmDeleteElementBody' => Yii::t('CustomPagesModule.modules_template_widgets_views_confirmDeletionModal', 'Do you really want to delete this content?'),
                    'confirmDeleteItemHeader' => Yii::t('CustomPagesModule.modules_template_controller_OwnerContentController', '<strong>Confirm</strong> container item deletion'),
                    'confirmDeleteItemBody' => Yii::t('CustomPagesModule.modules_template_widgets_views_confirmDeletionModal', 'Are you sure you want to delete this container item?'),
                ]
            ]);
        }

        return \yii\helpers\Html::tag('div', ob_get_clean(), $this->getOptions());
    }

    /**
     * @inheritdoc
     */
    public function getAttributes()
    {
        //TODO: fullscreen flag
        if($this->page instanceof Page && !$this->contentContainer && $this->page->getTargetId() !== Page::NAV_CLASS_ACCOUNTNAV) {
            $cssClass = 'container ';
        } else {
            $cssClass = '';
        }

        
        $cssClass .= ($this->page->hasAttribute('cssClass') && !empty($this->page->cssClass)) ? $this->page->cssClass : 'custom-pages-page';
        return [
            'class' => $cssClass
        ];
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        if ($this->canEdit && $this->editMode) {
            return [
                'element-edit-url' => $this->createUrl('/custom_pages/template/owner-content/edit'),
                'element-delete-url' => $this->createUrl('/custom_pages/template/owner-content/delete'),
                'create-container-url' => $this->createUrl('/custom_pages/template/container-content/create-container'),
                'item-delete-url' => $this->createUrl('/custom_pages/template/container-content/delete-item'),
                'item-edit-url' => $this->createUrl('/custom_pages/template/container-content/edit-item'),
                'item-add-url' => $this->createUrl('/custom_pages/template/container-content/add-item'),
                'item-move-url' => $this->createUrl('/custom_pages/template/container-content/move-item'),
            ];
        }
        return [];
    }

    private function createUrl($route)
    {
        return ($this->contentContainer) ? $this->contentContainer->createUrl($route) : \yii\helpers\Url::to([$route]);
    }

}
