<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\widgets;

use humhub\components\Widget;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;

class TemplateStructure extends Widget
{
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
        $template = $this->templateInstance->template;

        return $this->render('templateStructure', [
            'template' => $template,
            'elementContents' => $template->getElementContents($this->templateInstance),
        ]);
    }
}
