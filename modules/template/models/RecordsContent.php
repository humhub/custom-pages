<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\models;

use humhub\components\ActiveRecord;
use humhub\libs\Html;
use humhub\modules\custom_pages\modules\template\widgets\TemplateContentFormFields;
use Yii;
use yii\db\IntegrityException;
use yii\helpers\ArrayHelper;

/**
 * Class RecordsContent
 *
 * @property string $type
 * @property string $class
 * @property string $options
 */
abstract class RecordsContent extends TemplateContentActiveRecord
{
    public const RECORD_CLASS = null;

    /**
     * @var ActiveRecord[]|null
     */
    protected ?array $records = null;

    /**
     * @var string Prefix for view file to render a widget with form fields
     */
    public string $formView = '';

    protected ?array $arrayOptions = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'custom_pages_template_records_content';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'class', 'options'], 'string'],
            [['class'], 'required'],
            [['type'], 'in', 'range' => array_keys($this->getTypes())],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'type' => Yii::t('CustomPagesModule.template', 'Type'),
        ];
    }

    protected function getScenarioAttributes(?string $scenario = null): array
    {
        return ['type', 'class', 'options'];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return ArrayHelper::merge(parent::scenarios(), [
            self::SCENARIO_CREATE => $this->getScenarioAttributes(self::SCENARIO_CREATE),
            self::SCENARIO_EDIT_ADMIN => $this->getScenarioAttributes(self::SCENARIO_EDIT_ADMIN),
            self::SCENARIO_EDIT => $this->getScenarioAttributes(self::SCENARIO_EDIT),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return static::$label;
    }

    /**
     * @inheritdoc
     */
    public function copy()
    {
        $clone = new static();
        $clone->type = $this->type;
        $clone->class = $this->class;
        $clone->options = $this->options;
        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function render($options = [])
    {
        return Html::encode($this->class);
    }

    /**
     * @inheritdoc
     */
    public function renderEmpty($options = [])
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function renderForm($form)
    {
        return TemplateContentFormFields::widget([
            'type' => 'records',
            'form' => $form,
            'model' => $this,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function setAttributes($values, $safeOnly = true)
    {
        if (isset($values['guid'])) {
            $values['guid'] = is_array($values['guid']) ? array_shift($values['guid']) : null;
        }

        parent::setAttributes($values, $safeOnly);
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();
        $this->initArrayOptions();
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        $this->class = static::RECORD_CLASS;
        return parent::beforeValidate();
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $this->options = json_encode($this->arrayOptions);
        return parent::beforeSave($insert);
    }

    /**
     * Get records
     *
     * @return ActiveRecord[]
     * @throws IntegrityException
     */
    public function getRecords(): array
    {
        if ($this->records === null) {
            $this->records = Yii::createObject($this->class)->find()->all();
        }

        return $this->records;
    }

    /**
     * Get types for the records list
     *
     * @return array
     */
    public function getTypes(): array
    {
        return [
            'static' => Yii::t('CustomPagesModule.template', 'Static list'),
        ];
    }

    protected function initArrayOptions(): void
    {
        if ($this->arrayOptions === null) {
            $this->arrayOptions = empty($this->options) ? [] : json_decode($this->options, true);
        }
    }

    public function getArrayOption(string $key, $default = null): mixed
    {
        return $this->arrayOptions[$key] ?? $default;
    }

    public function setArrayOption(string $key, $value): void
    {
        $this->arrayOptions[$key] = $value;
    }
}
