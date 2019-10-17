<?php

namespace humhub\modules\custom_pages\modules\template\models;

use humhub\libs\Html;
use Yii;
use humhub\modules\custom_pages\modules\template\widgets\TemplateContentFormFields;

/**
 * Class TextContent
 * @property bool $inline_text
 * @property string content
 */
class TextContent extends TemplateContentActiveRecord
{

    public static $label = 'Text';

    public function init()
    {
        parent::init();
        if($this->isNewRecord) {
            $this->inline_text = 1;
        }
    }

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
        $result[] = ['content', 'trim'];
        $result[] = ['inline_text', 'boolean'];
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

        $scenarios[self::SCENARIO_CREATE][] = 'inline_text';
        $scenarios[self::SCENARIO_EDIT_ADMIN][] = 'inline_text';

        // We disallow editing this field in page editor
        //$scenarios[self::SCENARIO_EDIT][] = 'inline_text';

        return $scenarios;
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'content' => 'Content',
            'inline_text' => 'Is inline text',
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
        $clone->inline_text = $this->inline_text;
        return $clone;
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
        if($this->inline_text) {
            $options['class'] = 'emptyBlock text';
            return $this->renderEmptyDiv(Yii::t('CustomPagesModule.models_RichtextContent', 'Empty Text'), $options);
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
                    'model' => $this
        ]);
    }

}
