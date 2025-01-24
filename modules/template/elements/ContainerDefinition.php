<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\custom_pages\modules\template\models\Template;
use Yii;

/**
 * Class for template Container element content definition
 *
 * Dynamic attributes:
 * @property array $templates IDs of allowed templates
 * @property bool $allow_multiple
 * @property bool $is_inline
 *
 * @property-read Template[] $allowedTemplates
 */
class ContainerDefinition extends BaseTemplateElementContentDefinition
{
    /**
     * @var Template[] Cached templates selected as allowed for this definition
     */
    private ?array $_templates = null;

    /**
     * @inheritdoc
     */
    protected function getDynamicAttributes(): array
    {
        return [
            'templates' => null,
            'allow_multiple' => null,
            'is_inline' => null,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['templates'], 'safe'],
            [['allow_multiple', 'is_inline'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'templates' => Yii::t('CustomPagesModule.template', 'Allowed Templates'),
            'allow_multiple' => Yii::t('CustomPagesModule.template', 'Allow multiple items?'),
            'is_inline' => Yii::t('CustomPagesModule.template', 'Render items as inline-blocks within the inline editor?'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
            'templates' => Yii::t('CustomPagesModule.base', 'An empty allowed template selection will allow all container templates for this container.'),
        ];
    }

    /**
     * @return Template[]
     */
    public function getAllowedTemplates(): array
    {
        if (empty($this->_templates)) {
            $condition = ['type' => Template::TYPE_CONTAINER];
            if (!empty($this->templates)) {
                $condition['name'] = $this->templates;
            }
            $this->_templates = Template::findAll($condition);
        }

        return $this->_templates;
    }

    public function isSingleAllowedTemplate(): bool
    {
        return is_array($this->templates) && count($this->templates) === 1;
    }
}
