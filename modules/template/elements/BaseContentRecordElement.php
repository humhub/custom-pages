<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\content\models\Content;
use humhub\modules\ui\form\widgets\ActiveForm;
use Yii;

/**
 * Abstract class to manage Element Content of the ContentActiveRecord
 *
 * @property-read ContentActiveRecord|null $record
 *
 * Dynamic attributes:
 * @property string $contentId
 */
abstract class BaseContentRecordElement extends BaseElementContent
{
    protected const RECORD_CLASS = ContentActiveRecord::class;

    /**
     * @inheritdoc
     */
    protected function getDynamicAttributes(): array
    {
        return [
            'contentId' => null,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['contentId'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
            'contentId' => Yii::t('CustomPagesModule.template', 'Content ID can be found in a permalink.'),
        ];
    }

    public function isEmpty(): bool
    {
        return parent::isEmpty() || !$this->record;
    }

    protected function getRecord(): ?ContentActiveRecord
    {
        if (empty($this->contentId)) {
            return null;
        }

        return Yii::$app->runtimeCache->getOrSet(self::class . $this->contentId, function () {
            return static::RECORD_CLASS::find()
                ->joinWith(Content::tableName())
                ->where([Content::tableName() . '.id' => $this->contentId])
                ->one();
        });
    }

    /**
     * @inheritdoc
     */
    public function getTemplateVariable(): BaseElementVariable
    {
        return BaseContentRecordElementVariable::instance($this)
            ->setRecord($this->getRecord());
    }

    /**
     * @inheritdoc
     */
    public function renderEditForm(ActiveForm $form): string
    {
        return $form->field($this, 'contentId');
    }
}
