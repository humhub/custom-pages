<?php

namespace humhub\modules\custom_pages\modules\template\models;

use yii\helpers\Url;

/**
 * This is the model class for table "custom_pages_template_container_content_item".
 * 
 * @var $template_id int
 * @var $container_content_id int
 * @var $title string
 */
class ContainerContentItem extends \humhub\components\ActiveRecord implements TemplateContentOwner
{

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'custom_pages_template_container_content_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['template_id', 'container_content_id'], 'required'],
            [['template_id', 'container_content_id', 'sort_order'], 'integer'],
            ['title', 'safe']
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

    public static function incrementIndex($cotnainerId, $index)
    {
        self::updateAllCounters(['sort_order' => 1], ['and', ['>=', 'sort_order', $index], ['container_content_id' => $cotnainerId]]);
    }
    
    public static function incrementBetween($cotnainerId, $start, $end)
    {
        self::updateAllCounters(['sort_order' => 1], ['and', ['>=', 'sort_order', $start], ['<=', 'sort_order', $end], ['container_content_id' => $cotnainerId]]);
    }
    
    public static function decrementIndex($cotnainerId, $index)
    {
        self::updateAllCounters(['sort_order' => -1], ['and', ['<=', 'sort_order', $index], ['container_content_id' => $cotnainerId]]);
    }
    
    public static function decrementBetween($cotnainerId, $start, $end)
    {
        self::updateAllCounters(['sort_order' => -1], ['and', ['>=', 'sort_order', $start], ['<=', 'sort_order', $end], ['container_content_id' => $cotnainerId]]);
    }

    public function getTemplate()
    {
        return $this->hasOne(Template::className(), ['id' => 'template_id']);
    }

    public function getContainer()
    {
        return $this->hasOne(ContainerContent::className(), ['id' => 'container_content_id']);
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
                'data-template-owner' => ContainerContent::className(),
                'data-template-owner-id' => $this->container_content_id
            ]
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
}
