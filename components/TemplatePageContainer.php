<?php

namespace humhub\modules\custom_pages\components;

use humhub\modules\custom_pages\types\TemplateType;
use Yii;

trait TemplatePageContainer
{
    /**
     * Validates the templateId value for template based pages.
     *
     * @param $attribute
     * @param $params
     */
    public function validateTemplateType($attribute, $params)
    {
        if (TemplateType::isType($this->type) && $this->isNewRecord && !$this->templateId) {
            $this->addError('templateId', Yii::t('CustomPagesModule.base', 'Invalid template selection!'));
        }
    }
}
