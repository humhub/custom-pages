<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\helpers\Html;
use humhub\widgets\form\ActiveForm;
use Yii;
use yii\db\ActiveQuery;

/**
 * Abstract class to manage content records of the elements with different object list (Spaces, Users)
 *
 * Dynamic attributes:
 * @property string $type
 * @property array $static
 */
abstract class BaseContentContainersElement extends BaseRecordsElement
{
    /**
     * @inheritdoc
     */
    protected function getDynamicAttributes(): array
    {
        return [
            'type' => null,
            'static' => null,
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
    public function rules()
    {
        return [
            [['type'], 'in', 'range' => array_keys($this->getTypes())],
            [['static'], 'safe'],
        ];
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

                if ($this->type !== 'static' && !empty($this->limit)) {
                    // Limit only dynamic list
                    $query->limit($this->limit);
                }

                $this->records = $query->all();
            }
        }

        yield from $this->records;
    }

    /**
     * Filter the list with static selected record
     *
     * @param ActiveQuery $query
     * @return ActiveQuery
     */
    protected function filterStatic(ActiveQuery $query): ActiveQuery
    {
        return $query->andWhere(['guid' => $this->static]);
    }

    /**
     * Check if the Element is properly configured
     *
     * @return bool
     */
    protected function isConfigured(): bool
    {
        return !empty($this->{$this->type});
    }

    /**
     * @inheritdoc
     */
    public function renderEditForm(ActiveForm $form): string
    {
        return $form->field($this, 'type')->dropDownList($this->getTypes(), ['class' => 'records-content-form-type']) .
            Html::script(<<<JS
    $(document).on('change', '.records-content-form-type', function () {
        const type = $(this).val();
        $(this).closest('form').find('.records-content-form-fields').each(function () {
            $(this).toggle($(this).data('type').match(new RegExp('(^|,)' + type + '(,|$)')) !== null);
        });
    });
    $('.records-content-form-type').trigger('change');
JS);
    }

    /**
     * Renders additional fields for the edit form per type
     *
     * @param array $fields
     * @return string
     */
    protected function renderEditRecordsTypeFields(array $fields): string
    {
        $result = '';

        foreach ($fields as $type => $field) {
            $result .= Html::tag('div', $field, [
                'class' => 'records-content-form-fields',
                'data-type' => $type,
            ]);
        }

        return $result;
    }
}
