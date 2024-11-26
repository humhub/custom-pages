<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\models;

use humhub\libs\Html;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\models\ContentContainer;
use humhub\modules\custom_pages\modules\template\widgets\TemplateContentFormFields;
use humhub\modules\user\models\User;
use yii\db\IntegrityException;
use yii\helpers\ArrayHelper;

/**
 * Class ContentContainerContent
 *
 * @property string $guid
 * @property string $class
 */
abstract class ContentContainerContent extends TemplateContentActiveRecord
{
    public const CONTAINER_CLASS = null;

    protected ?ContentContainerActiveRecord $record = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'custom_pages_template_contentcontainer_content';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['guid', 'class'], 'string'],
            [['class'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return ArrayHelper::merge(parent::scenarios(), [
            self::SCENARIO_CREATE => $attributes = ['guid', 'class'],
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
        $clone->guid = $this->guid;
        $clone->class = $this->class;
        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function render($options = [])
    {
        return Html::encode($this->getRecord()->getDisplayName());
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
            'type' => strtolower(substr(strrchr(static::CONTAINER_CLASS, '\\'), 1)),
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
    public function beforeValidate()
    {
        $this->class = static::CONTAINER_CLASS;
        return parent::beforeValidate();
    }

    /**
     * Get a related record(User or Space) to the container
     *
     * @return ContentContainerActiveRecord|null
     * @throws IntegrityException
     */
    public function getRecord(?string $guid = null): ?ContentContainerActiveRecord
    {
        if ($this->record === null) {
            $this->record = ContentContainer::findRecord($guid ?: $this->guid);
        }

        return $this->record;
    }
}
