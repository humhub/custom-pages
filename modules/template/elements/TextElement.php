<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\libs\Html;
use humhub\modules\custom_pages\modules\template\widgets\TemplateContentFormFields;
use Yii;

/**
 * Class to manage content records of the text elements
 *
 * Element content fields:
 * @property-read bool $inline_text
 * @property-read string $content
 */
class TextElement extends BaseTemplateElementContent
{
    public static $label = 'Text';

    /**
     * @inheritdoc
     */
    protected function getFields(): array
    {
        return [
            'content' => null,
            'inline_text' => 1,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['content', 'trim'],
            ['inline_text', 'boolean'],
            ['content', 'string', 'length' => [1, 255]],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE][] = 'content';
        $scenarios[self::SCENARIO_EDIT_ADMIN][] = 'content';
        $scenarios[self::SCENARIO_EDIT][] = 'content';

        $scenarios[self::SCENARIO_CREATE][] = 'inline_text';
        $scenarios[self::SCENARIO_EDIT_ADMIN][] = 'inline_text';

        // We disallow editing this field in page editor
        //$scenarios[self::SCENARIO_EDIT][] = 'inline_text';

        return $scenarios;
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
        $result = $this->inline_text ? $this->purify($this->content) : Html::encode($this->content);

        if ($this->isEditMode($options) && $this->inline_text) {
            return $this->wrap('span', $result, $options);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function renderEmpty($options = [])
    {
        if ($this->inline_text) {
            $options['class'] = 'emptyBlock text';
            return $this->renderEmptyDiv(Yii::t('CustomPagesModule.model', 'Empty Text'), $options);
        }

        return '';
    }

    /**
     * @inheritdoc
     */
    public function renderForm($form)
    {
        return TemplateContentFormFields::widget([
            'type' => 'text',
            'form' => $form,
            'model' => $this,
        ]);
    }

}
