<?php


namespace humhub\modules\custom_pages\components;


use humhub\modules\custom_pages\models\IframeType;
use Yii;

trait IframePageContainer
{
    /**
     * Validates the allow_attribute value for Iframe based pages.
     *
     * @param $attribute
     * @param $params
     */
    public function validateIframeType($attribute, $params)
    {
        if (IframeType::isType($this->type))
        {
            $allow_attribute = '';
            foreach($this->allow_attribute as $value) {
                if (!IframeType::isAllowAttribute($value)) {
                    $this->addError('allow_iframe', Yii::t('CustomPagesModule.components_Container', 'Invalid permissions selection!'));
                } else {
                    $allow_attribute .= $value . ' ';
                }
            }
            $this->allow_attribute = rtrim($allow_attribute);
        }
    }
}