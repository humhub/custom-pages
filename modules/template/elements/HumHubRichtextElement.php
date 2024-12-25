<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\custom_pages\modules\template\widgets\TemplateContentFormFields;
use Yii;

/**
 * Class to manage content records of the HumHub RichText elements
 *
 * Dynamic attributes:
 * @property string $content
 */
class HumHubRichtextElement extends BaseTemplateElementContent
{
    public static $label = 'HumHub Richtext';

    /**
     * @inheritdoc
     */
    protected function getDynamicAttributes(): array
    {
        return [
            'content' => null,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['content', 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return  [
            'content' => Yii::t('CustomPagesModule.template', 'Content'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return self::$label;
    }

    /**
     * @inheritdoc
     */
    public function render($options = [])
    {
        if ($this->isEditMode($options)) {
            return $this->wrap('div', Richtext::output($this->content), $options);
        }

        return Richtext::output($this->content);
    }

    /**
     * @inheritdoc
     */
    public function saveFiles()
    {
        Richtext::postProcess($this->content, $this);
    }

    /**
     * @inheritdoc
     */
    public function renderEmpty($options = [])
    {
        return $this->renderEmptyDiv(Yii::t('CustomPagesModule.model', 'Empty HumHub Richtext'), $options);
    }

    /**
     * @inheritdoc
     */
    public function renderForm($form)
    {
        return TemplateContentFormFields::widget([
            'type' => 'humhubRichtext',
            'form' => $form,
            'model' => $this,
        ]);
    }

}
