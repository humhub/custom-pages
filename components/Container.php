<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace humhub\modules\custom_pages\components;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;

/**
 * Description of AbstractContainer
 *
 * @author buddha
 */
class Container extends Behavior
{

    const TYPE_LINK = '1';
    const TYPE_HTML = '2';
    const TYPE_IFRAME = '3';
    const TYPE_MARKDOWN = '4';
    const TYPE_TEMPLATE = '5';

    public $templateId;
    public $contentProp = 'content';

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    public function defaultRules()
    {
        return [
            [['type', 'title'], 'required'],
            [['type', 'sort_order', 'admin_only'], 'integer'],
            [['title', 'cssClass'], 'string', 'max' => 255],
            [['icon'], 'string', 'max' => 100],
            [['templateId'], 'safe'],
            ['type', 'validateTemplateType'],
        ];
    }

    public function validateTemplateType($attribute, $params)
    {
        if ($this->owner->isNewRecord && $this->owner->type == self::TYPE_TEMPLATE && $this->owner->templateId == null) {
            $this->owner->addError('templateId', Yii::t('CustomPagesModule.components_Container', 'Invalid template selection!'));
        }
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function defaultAttributeLabels()
    {
        return [
            'id' => Yii::t('CustomPagesModule.components_Container', 'ID'),
            'type' => Yii::t('CustomPagesModule.components_Container', 'Type'),
            'title' => Yii::t('CustomPagesModule.components_Container', 'Title'),
            'icon' => Yii::t('CustomPagesModule.components_Container', 'Icon'),
            'cssClass' => Yii::t('CustomPagesModule.components_Container', 'Style Class'),
            'content' => Yii::t('CustomPagesModule.components_Container', 'Content'),
            'templateId' => Yii::t('CustomPagesModule.components_Container', 'Template Layout'),
            'sort_order' => Yii::t('CustomPagesModule.components_Container', 'Sort Order'),
            'targetUrl' => Yii::t('CustomPagesModule.components_Container', 'Target Url'),
            'templateId' => Yii::t('CustomPagesModule.components_Container', 'Template Layout'),
            'admin_only' => Yii::t('CustomPagesModule.models_Page', 'Only visible for admins')
        ];
    }

    public function afterSave()
    {
        if ($this->owner->type == self::TYPE_TEMPLATE) {
            $container = new TemplateInstance();
            $container->object_model = $this->owner->className();
            $container->object_id = $this->owner->id;
            $container->template_id = $this->owner->templateId;
            $container->save();
        }
    }

    public function afterDelete()
    {
        if ($this->owner->type == self::TYPE_TEMPLATE) {
            TemplateInstance::deleteByOwner($this->owner);
        }
    }

    public function defaultPageTypes()
    {
        return [
            self::TYPE_MARKDOWN => Yii::t('CustomPagesModule.base', 'MarkDown'),
            self::TYPE_LINK => Yii::t('CustomPagesModule.base', 'Link'),
            self::TYPE_IFRAME => Yii::t('CustomPagesModule.base', 'IFrame'),
            self::TYPE_TEMPLATE => Yii::t('CustomPagesModule.base', 'Template'),
        ];
    }

    public function isType($type)
    {
        return $this->owner->type == $type;
    }

    public static function contentTypes()
    {
        return [
            Container::TYPE_LINK => Yii::t('CustomPagesModule.base', 'Link'),
            Container::TYPE_HTML => Yii::t('CustomPagesModule.base', 'HTML'),
            Container::TYPE_MARKDOWN => Yii::t('CustomPagesModule.base', 'MarkDown'),
            Container::TYPE_IFRAME => Yii::t('CustomPagesModule.base', 'IFrame'),
            Container::TYPE_TEMPLATE => Yii::t('CustomPagesModule.base', 'Template'),
        ];
    }
    
    /**
     * @return array view names by page type
     */
    private static function viewNames()
    {
        return [
            Container::TYPE_HTML => 'html',
            Container::TYPE_MARKDOWN => 'markdown',
            Container::TYPE_IFRAME => 'iframe',
            Container::TYPE_TEMPLATE => 'template'
        ];
    }

    public function getTemplateId()
    {
        if($this->templateId == null) {
            $templateInstance = TemplateInstance::findByOwner($this->owner);
            if($templateInstance) {
                $this->owner->templateId = $templateInstance->template_id;
            }
        }
        return $this->templateId;
    }

    public function setTemplateId($value)
    {
        return $this->templateId = $value;
    }
    
    public static function getViewName($type)
    {
        return self::viewNames()[$type];
    }

    public static function getLabel($type)
    {
        return self::contentTypes()[$type];
    }
}
