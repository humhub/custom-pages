<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\widgets;

use humhub\modules\custom_pages\modules\template\elements\ContainerElement;
use humhub\widgets\JsWidget;
use Yii;
use yii\helpers\Url;

/**
 * Lists the items of a container element of a template instance
 * and allows to edit, sort, add and delete them.
 */
class ContainerItemsList extends JsWidget
{
    /**
     * @inheritdoc
     */
    public $jsWidget = 'custom_pages.template.ContainerItemsList';

    /**
     * @inheritdoc
     */
    public $init = true;

    public ?ContainerElement $containerElement = null;

    /**
     * @inheritdoc
     */
    public function beforeRun()
    {
        return parent::beforeRun() && $this->containerElement?->templateInstance !== null;
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('containerItemsList', [
            'container' => $this->containerElement,
            'options' => $this->getOptions(),
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function getData()
    {
        $templateInstance = $this->containerElement->templateInstance;

        return [
            'container-id' => $this->containerElement->isNewRecord ? null : $this->containerElement->id,
            'element-id' => $this->containerElement->element_id,
            'template-instance-id' => $templateInstance->id,
            'allow-multiple' => (int) ($this->containerElement->definition?->allow_multiple ?? 0),
            'item-move-url' => $this->createUrl('/custom_pages/template/container-content/move-item'),
            'item-delete-url' => $this->createUrl('/custom_pages/template/container-content/delete-item'),
            'elements-edit-url' => $this->createUrl('/custom_pages/template/element-content/edit-multiple'),
            // The structure view implements the flow of adding an item, so its button is reused from there
            'add-button-selector' => '.cp-structure [data-template-instance-id="' . $templateInstance->id . '"] > li > ul > li[data-element-id="' . $this->containerElement->element_id . '"] > .cp-structure-container > [data-action-click="addContainerItem"]',
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getAttributes()
    {
        return [
            'class' => 'cp-container-items',
        ];
    }

    private function createUrl($route): string
    {
        $container = Yii::$app->controller->contentContainer ?? $this->containerElement->templateInstance?->page?->content?->container;
        return $container ? $container->createUrl($route) : Url::to([$route]);
    }
}
