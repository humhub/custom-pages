<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\widgets;

use humhub\modules\custom_pages\modules\template\elements\BaseElementContent;
use humhub\modules\custom_pages\modules\template\elements\ContainerElement;
use humhub\modules\custom_pages\modules\template\elements\ContainerItem;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use humhub\widgets\JsWidget;
use Yii;
use yii\helpers\Url;

class TemplateStructure extends JsWidget
{
    /**
     * @inheritdoc
     */
    public $jsWidget = 'custom_pages.template.TemplateStructure';

    /**
     * @inheritdoc
     */
    public $init = true;

    /**
     * @var TemplateInstance|null
     */
    public ?TemplateInstance $templateInstance = null;

    public ?int $level = null;

    /**
     * @inheritdoc
     */
    public function beforeRun()
    {
        return parent::beforeRun() && $this->templateInstance !== null;
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $containers = array_filter($this->templateInstance->template->getElementContents($this->templateInstance), function ($element) {
            return $element instanceof ContainerElement;
        });

        return $this->render('templateStructure', [
            'templateInstance' => $this->templateInstance,
            'containers' => $containers,
            'options' => $this->getOptions(),
            'templateInstanceOptions' => $this->getTemplateInstanceOptions(),
            'level' => $this->getLevel(),
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function getData()
    {
        return [
            'elements-edit-url' => $this->createUrl('/custom_pages/template/element-content/edit-multiple'),
            'create-container-url' => $this->createUrl('/custom_pages/template/container-content/create-container'),
            'item-add-url' => $this->createUrl('/custom_pages/template/container-content/add-item'),
            'item-move-url' => $this->createUrl('/custom_pages/template/container-content/move-item'),
            'item-delete-url' => $this->createUrl('/custom_pages/template/container-content/delete-item'),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getAttributes()
    {
        return [
            'class' => 'panel cp-structure',
        ];
    }

    public function getTemplateInstanceOptions(): array
    {
        $options = [
            'data-template-instance-id' => $this->templateInstance->id,
            'data-template-type' => $this->templateInstance->template->type,
        ];

        if ($this->templateInstance->isContainer()) {
            $containerItem = $this->templateInstance->containerItem;
            if ($containerItem instanceof ContainerItem) {
                $options['data-container-item-id'] = $containerItem->id;
                $options['data-element-id'] = $containerItem->container->element_id;
                $options['data-container-id'] = $containerItem->element_content_id;
            }
        }

        return $options;
    }

    public function getContainerOptions(ContainerElement $element): array
    {
        return [
            'data-element-id' => $element->element_id,
            'data-container-id' => $element->id,
            'data-default' => $element->isDefault(),
            'data-allow-multiple' => $element->definition->allow_multiple,
        ];
    }

    private function createUrl($route): string
    {
        $container = Yii::$app->controller->contentContainer ?? $this->templateInstance?->page?->content?->container;
        return $container ? $container->createUrl($route) : Url::to([$route]);
    }

    private function getLevel(): int
    {
        if ($this->level === null) {
            $this->level = 0;
            $containerItem = $this->templateInstance->containerItem;
            while ($containerItem) {
                $containerItem = $containerItem->container->templateInstance->containerItem;
                $this->level += 2;
            }
        }

        return $this->level;
    }
}
