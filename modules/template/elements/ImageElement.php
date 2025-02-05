<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\libs\Html;
use Yii;

/**
 * Class to manage content records of the Image elements
 *
 * Dynamic attributes:
 * @property string $alt
 */
class ImageElement extends FileElement
{
    /**
     * @inheritdoc
     */
    public function getLabel(): string
    {
        return Yii::t('CustomPagesModule.template', 'Image');
    }

    /**
     * @inheritdoc
     */
    public $definitionModel = ImageDefinition::class;

    /**
     * @inheritdoc
     */
    protected function getDynamicAttributes(): array
    {
        return array_merge(parent::getDynamicAttributes(), [
            'alt' => null,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $result = [];
        // We prevent the content instance from being saved if there is no definition setting, to get sure we have an empty content in this case
        // TODO: perhaps overwrite the validate method and call parent validate only if no definition is set
        if ($this->definition == null || !$this->definition->hasValues()) {
            $result[] = [['file_guid'], 'required'];
        }
        $result[] = [['alt', 'file_guid'], 'safe'];
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'alt' =>  Yii::t('CustomPagesModule.base', 'Alternate text'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function render($options = [])
    {
        if ($this->hasFile() != null) {
            $options['htmlOptions'] = [
                'src' => $this->getFile()->getUrl(),
                'alt' => $this->purify($this->alt),
            ];

            $options['htmlOptions']['height'] = $this->purify($this->definition->height);
            $options['htmlOptions']['width'] = $this->purify($this->definition->width);
            $options['htmlOptions']['style'] = $this->purify($this->definition->style);

            return $this->isEditMode($options)
                ? $this->wrap('img', '', $options)
                : Html::tag('img', '', $options['htmlOptions']);
        } elseif ($this->isEditMode($options)) {
            $options['empty'] = true;
            return $this->renderEmpty($options);
        }

        return '';
    }

    /**
     * @inheritdoc
     */
    public function renderEmpty($options = [])
    {
        return $this->renderEmptyDiv(Yii::t('CustomPagesModule.model', 'Empty Image'), $options);
    }
}
