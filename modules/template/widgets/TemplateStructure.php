<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\widgets;

use humhub\modules\custom_pages\modules\template\elements\ContainerItem;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use humhub\widgets\JsWidget;
use Yii;

class TemplateStructure extends JsWidget
{
    /**
     * @inheritdoc
     */
    public $jsWidget = 'custom_pages.template.TemplateStructure';

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

        $containerItem = $this->templateInstance->containerItem;
        if ($containerItem instanceof ContainerItem) {
            $data['container-item-id'] = $containerItem->id;
            $data['element-id'] = $containerItem->container->element_id;
            $data['element-content-id'] = $containerItem->element_content_id;
        }

        return $data;
    }
}
