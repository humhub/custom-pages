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
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * Class RecordsContent
 *
 * @property string $type
 * @property string $class
 * @property string|array $options
 */
abstract class RecordsContent extends TemplateContentActiveRecord implements TemplateContentIterable
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

    /**
     * Get query of the records depending on config
     *
     * @return ActiveQuery
     */
    abstract protected function getQuery(): ActiveQuery;

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
            [['type', 'class'], 'string'],
            [['options'], 'safe'],
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

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return ArrayHelper::merge(parent::scenarios(), [
            self::SCENARIO_CREATE => $attributes = ['type', 'options'],
            self::SCENARIO_EDIT_ADMIN => $attributes,
            self::SCENARIO_EDIT => $attributes,
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
    public function __get($name)
    {
        $value = parent::__get($name);

        if ($name === 'options' && !is_array($value)) {
            $value = empty($value) ? [] : json_decode($value, true);
            $this->setAttribute($name, $value);
        }

        return $value;
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
        if (parent::beforeSave($insert)) {
            $this->options = is_array($this->options) ? json_encode($this->options) : null;
            return true;
        }

        return false;
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

    /**
     * @inheritdoc
     */
    public function getItems(): iterable
    {
        if ($this->records === null) {
            if (!$this->isConfigured()) {
                // No need to touch DB because option is not configured for the current type
                $this->records = [];
            } else {
                // Get records from DB
                $query = $this->getQuery();

                if ($this->type !== 'static' && !empty($this->options['limit'])) {
                    // Limit only dynamic list
                    $query->limit($this->options['limit']);
                }

                $this->records = $query->all();
            }
        }

        yield from $this->records;
    }

    protected function filterStatic(ActiveQuery $query): ActiveQuery
    {
        return $query->andWhere(['guid' => $this->options['static']]);
    }

    /**
     * Check if the Element is properly configured
     *
     * @return bool
     */
    protected function isConfigured(): bool
    {
        return !empty($this->options[$this->type]);
    }
}
