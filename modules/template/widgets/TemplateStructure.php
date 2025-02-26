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
use humhub\modules\custom_pages\modules\template\models\Template;
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
        return $this->render('templateStructure', [
            'templateInstance' => $this->templateInstance,
            'elementContents' => $this->templateInstance->template->getElementContents($this->templateInstance),
            'sguid' => Yii::$app->controller->contentContainer->guid ?? null,
            'options' => $this->getOptions(),
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function getData()
    {
        $data = parent::getData();
        $data['template-instance-id'] = $this->templateInstance->id;
        $data['template-type'] = $this->templateInstance->template->type;

        $containerItem = $this->templateInstance->containerItem;
        if ($containerItem instanceof ContainerItem) {
            $data['container-item-id'] = $containerItem->id;
            $data['element-id'] = $containerItem->container->element_id;
            $data['element-content-id'] = $containerItem->element_content_id;
        } else {
            $data['create-container-url'] = $this->createUrl('/custom_pages/template/container-content/create-container');
            $data['item-add-url'] = $this->createUrl('/custom_pages/template/container-content/add-item');
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    protected function getOptions()
    {
        $options = parent::getOptions();

        if ($this->templateInstance->container_item_id === null) {
            $options['class'] = 'custom-pages-template-structure';
        }

        return $options;
    }

    public function getElementContentOptions(BaseElementContent $elementContent): array
    {
        $options = [];

        if ($elementContent instanceof ContainerElement) {
            $options['data-element-id'] = $elementContent->element_id;
            $options['data-element-content-id'] = $elementContent->id;
            $options['data-default'] = $elementContent->isDefault();
        }

        return $options;
    }

    private function createUrl($route): string
    {
        return Yii::$app->controller->contentContainer
            ? Yii::$app->controller->contentContainer->createUrl($route)
            : Url::to([$route]);
    }
}
