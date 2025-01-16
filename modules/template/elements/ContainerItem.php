<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\components\ActiveRecord;
use humhub\modules\custom_pages\modules\template\models\OwnerContent;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\models\TemplateContentOwner;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use yii\helpers\Url;

/**
 * This is the model class for table "custom_pages_template_element_container_item".
 *
 * @property int $id
 * @property int $template_id
 * @property int $element_content_id
 * @property int $sort_order
 * @property string $title
 *
 * @property-read ContainerElement $container
 * @property-read Template $template
 */
class ContainerItem extends ActiveRecord implements TemplateContentOwner
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'custom_pages_template_element_container_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['template_id', 'element_content_id'], 'required'],
            [['template_id', 'element_content_id', 'sort_order'], 'integer'],
            ['title', 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        OwnerContent::deleteByOwner($this);
        parent::afterDelete();
    }

    public static function incrementIndex($containerId, $index)
    {
        self::updateAllCounters(['sort_order' => 1], ['and', ['>=', 'sort_order', $index], ['element_content_id' => $containerId]]);
    }

    public static function incrementBetween($containerId, $start, $end)
    {
        self::updateAllCounters(['sort_order' => 1], ['and', ['>=', 'sort_order', $start], ['<=', 'sort_order', $end], ['element_content_id' => $containerId]]);
    }

    public static function decrementIndex($containerId, $index)
    {
        self::updateAllCounters(['sort_order' => -1], ['and', ['<=', 'sort_order', $index], ['element_content_id' => $containerId]]);
    }

    public static function decrementBetween($containerId, $start, $end)
    {
        self::updateAllCounters(['sort_order' => -1], ['and', ['>=', 'sort_order', $start], ['<=', 'sort_order', $end], ['element_content_id' => $containerId]]);
    }

    public function getTemplate()
    {
        return $this->hasOne(Template::class, ['id' => 'template_id']);
    }

    public function getContainer()
    {
        return $this->hasOne(ContainerElement::class, ['id' => 'element_content_id']);
    }

    public function render($editMode, $inline = false)
    {
        if ($editMode) {
            return $this->wrap($this->template->render($this, $editMode), $inline);
        }

        return $this->template->render($this, $editMode, $this);
    }

    public function wrap($content, $inline)
    {
        return \humhub\widgets\JsWidget::widget([
            'jsWidget' => 'custom_pages.template.TemplateContainerItem',
            'content' => $content,
            'options' => [
                'class' => ($inline) ? 'inline' : '',
                'data-allow-inline-activation' => $this->template->allow_inline_activation,
                'data-template-item' => $this->id,
                'data-template-edit-url' => Url::to(['/custom_pages/template/container-admin/edit-source', 'id' => $this->template_id]),
                'data-template-item-title' => $this->title,
                'data-template-owner' => ContainerElement::class,
                'data-template-owner-id' => $this->element_content_id,
            ],
        ]);
    }

    public function getTemplateId()
    {
        return $this->template_id;
    }

    public static function findByTemplateId($templateId)
    {
        return self::find()->where(['template_id' => $templateId]);
    }

    public function getTemplateInstance(): ?TemplateInstance
    {
        $container = $this->container;
        if ($container instanceof ContainerElement) {
            $ownerContent = $container->ownerContent;
            if ($ownerContent instanceof OwnerContent) {
                $owner = $ownerContent->getOwner();
                if ($owner instanceof TemplateInstance) {
                    return $owner;
                }
            }
        }

        return null;
    }
}
