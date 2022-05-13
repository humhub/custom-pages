<?php
/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 13.02.2019
 * Time: 13:29
 */

namespace humhub\modules\custom_pages\models;


use humhub\modules\ui\form\widgets\MultiSelect;
use yii\widgets\ActiveForm;
use Yii;

class IframeType extends ContentType
{

    const ID = 3;

    protected $hasContent = false;

    private $availableAllowAttribute = null;

    function getId()
    {
        return static::ID;
    }

    function getLabel()
    {
        return Yii::t('CustomPagesModule.base', 'Iframe');
    }

    function getDescription()
    {
        return  Yii::t('CustomPagesModule.base', 'Will embed the the result of a given url as an iframe element.');
    }

    public function render(CustomContentContainer $content, $options = [])
    {
        // TODO: Implement render() method.
    }

    public function getViewName()
    {
        return 'iframe';
    }

    public function renderFormField(ActiveForm $form, CustomContentContainer $page)
    {
        return $form->field($page, $page->getPageContentProperty())->textInput(['class' => 'form-control'])->label($page->getAttributeLabel('targetUrl'))
            .'<div class="help-block">'.Yii::t('CustomPagesModule.views_common_edit', 'e.g. http://www.example.de').'</div>'
            . $form->field($page, 'allow_attribute')->widget(MultiSelect::class, [
                'items' => IframeType::getAvailableAllowAttributes(),
                'placeholderMore' => 'Select Permission...',
                'selection'=> explode(' ', $page->allow_attribute ?? ''),
                'maxSelection' => 50
            ]);;
    }

    static public function isAllowAttribute(string $allowAttribute)
    {
        return array_key_exists($allowAttribute, IframeType::getAvailableAllowAttributes());
    }

    static public function getAvailableAllowAttributes()
    {
        return [
            'accelerometer' => Yii::t('CustomPagesModule.views_common_edit', 'accelerometer'),
            'ambient-light-sensor' => Yii::t('CustomPagesModule.views_common_edit', 'ambient-light-sensor'),
            'autoplay' => Yii::t('CustomPagesModule.views_common_edit', 'autoplay'),
            'battery' => Yii::t('CustomPagesModule.views_common_edit', 'battery'),
            'camera' => Yii::t('CustomPagesModule.views_common_edit', 'camera'),
            'display-capture' => Yii::t('CustomPagesModule.views_common_edit', 'display-capture'),
            'document-domain' => Yii::t('CustomPagesModule.views_common_edit', 'document-domain'),
            'encrypted-media' => Yii::t('CustomPagesModule.views_common_edit', 'encrypted-media'),
            'execution-while-not-rendered' => Yii::t('CustomPagesModule.views_common_edit', 'execution-while-not-rendered'),
            'execution-while-out-of-viewport' => Yii::t('CustomPagesModule.views_common_edit', 'execution-while-out-of-viewport'),
            'fullscreen' => Yii::t('CustomPagesModule.views_common_edit', 'fullscreen'),
            'gamepad' => Yii::t('CustomPagesModule.views_common_edit', 'gamepad'),
            'geolocation' => Yii::t('CustomPagesModule.views_common_edit', 'geolocation'),
            'gyroscope' => Yii::t('CustomPagesModule.views_common_edit', 'gyroscope'),
            'layout-animations' => Yii::t('CustomPagesModule.views_common_edit', 'layout-animations'),
            'legacy-image-formats' => Yii::t('CustomPagesModule.views_common_edit', 'legacy-image-formats'),
            'magnetometer' => Yii::t('CustomPagesModule.views_common_edit', 'magnetometer'),
            'microphone' => Yii::t('CustomPagesModule.views_common_edit', 'microphone'),
            'midi' => Yii::t('CustomPagesModule.views_common_edit', 'midi'),
            'navigation-override' => Yii::t('CustomPagesModule.views_common_edit', 'navigation-override'),
            'oversized-images' => Yii::t('CustomPagesModule.views_common_edit', 'oversized-images'),
            'payment' => Yii::t('CustomPagesModule.views_common_edit', 'payment'),
            'picture-in-picture' => Yii::t('CustomPagesModule.views_common_edit', 'picture-in-picture'),
            'publickey-credentials-get' => Yii::t('CustomPagesModule.views_common_edit', 'publickey-credentials-get'),
            'speaker-selection' => Yii::t('CustomPagesModule.views_common_edit', 'speaker-selection'),
            'sync-xhr' => Yii::t('CustomPagesModule.views_common_edit', 'sync-xhr'),
            'unoptimized-images' => Yii::t('CustomPagesModule.views_common_edit', 'unoptimized-images'),
            'unsized-media' => Yii::t('CustomPagesModule.views_common_edit', 'unsized-media'),
            'usb' => Yii::t('CustomPagesModule.views_common_edit', 'usb'),
            'screen-wake-lock' => Yii::t('CustomPagesModule.views_common_edit', 'screen-wake-lock'),
            'web-share' => Yii::t('CustomPagesModule.views_common_edit', 'web-share'),
            'xr-spatial-tracking' => Yii::t('CustomPagesModule.views_common_edit', 'xr-spatial-tracking'),
        ];
    }
}