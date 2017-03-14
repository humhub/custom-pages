<?php

namespace humhub\modules\custom_pages\modules\template\models;

use Yii;
use humhub\modules\custom_pages\modules\template\widgets\TemplateContentFormFields;

class TextContent extends TemplateContentActiveRecord
{

    public static $label = 'Text';

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'custom_pages_template_text_content';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $result = parent::rules();
        $result[] = ['content', 'required'];
        $result[] = ['content', 'trim'];
        $result[] = ['content', 'string', 'length' => [1, 255]];
        return $result;
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
        return $scenarios;
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'content' => 'Content',
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
    public function copy()
    {
        $clone = new TextContent();
        $clone->content = $this->content;
        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function render($options = [])
    {
        if ($this->isEditMode($options)) {
            return $this->wrap('span', $this->purify($this->content), $options);
        }

        return $this->purify($this->content);
    }

    /**
     * @inheritdoc
     */
    public function renderEmpty($options = [])
    {
        $options['class'] = 'emptyBlock text';
        return $this->renderEmptyDiv(Yii::t('CustomPagesModule.models_RichtextContent', 'Empty Text'), $options);
    }

    /**
     * @inheritdoc
     */
    public function renderForm($form)
    {
        return TemplateContentFormFields::widget([
                    'type' => 'text',
                    'form' => $form,
                    'model' => $this
        ]);
    }

}
