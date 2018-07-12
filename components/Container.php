<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace humhub\modules\custom_pages\components;

use humhub\modules\custom_pages\models\forms\SettingsForm;
use humhub\modules\file\libs\FileHelper;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use yii\helpers\Html;
use yii\web\HttpException;

/**
 * The Container Behavior is used by custom page containers as pages and snippets and does provide some common
 * logic as default labels and rules as well as helper functions for special container types as template or php based
 * containers.
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
    const TYPE_PHP = '6';

    /**
     * @var ActiveRecord|null the owner of this behavior
     */
    public $owner;

    /**
     * @var integer special field for template based pages specifying the layout id
     */
    public $templateId;

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function defaultAttributeLabels()
    {
        if($this->isType(Container::TYPE_PHP)) {
            $contentLabel = Yii::t('CustomPagesModule.models_Page', 'View');
        } else {
            $contentLabel = Yii::t('CustomPagesModule.components_Container', 'Content');
        }

        return [
            'id' => Yii::t('CustomPagesModule.components_Container', 'ID'),
            'type' => Yii::t('CustomPagesModule.components_Container', 'Type'),
            'title' => Yii::t('CustomPagesModule.components_Container', 'Title'),
            'icon' => Yii::t('CustomPagesModule.components_Container', 'Icon'),
            'cssClass' => Yii::t('CustomPagesModule.components_Container', 'Style Class'),
            'content' => $contentLabel,
            'sort_order' => Yii::t('CustomPagesModule.components_Container', 'Sort Order'),
            'targetUrl' => Yii::t('CustomPagesModule.components_Container', 'Target Url'),
            'templateId' => Yii::t('CustomPagesModule.components_Container', 'Template Layout'),
            'admin_only' => Yii::t('CustomPagesModule.models_Page', 'Only visible for admins')
        ];
    }


    /**
     * Returns the default validation rules of a container, this may be overwritten or extended by subclasses.
     *
     * @return array
     */
    public function defaultRules()
    {
        return [
            [['type', 'title'], 'required'],
            [['type', 'sort_order', 'admin_only'], 'integer'],
            [['title', 'cssClass'], 'string', 'max' => 255],
            [['icon'], 'string', 'max' => 100],
            [['templateId'], 'safe'],
            ['type', 'validateTemplateType'],
            ['type', 'validatePhpType'],
        ];
    }

    /**
     * Validates the templateId value for template based pages.
     *
     * @param $attribute
     * @param $params
     */
    public function validateTemplateType($attribute, $params)
    {
        if ($this->isType(self::TYPE_TEMPLATE) && $this->owner->isNewRecord && !$this->owner->templateId) {
            $this->owner->addError('templateId', Yii::t('CustomPagesModule.components_Container', 'Invalid template selection!'));
        }
    }

    /**
     * Additional validator for php based pages.
     *
     * @param $attribute
     * @param $params
     */
    public function validatePhpType($attribute, $params)
    {
        if($this->isType(self::TYPE_PHP)) {
            $settigns = new SettingsForm();
            if($this->owner->isNewRecord && !$settigns->phpPagesActive) {
                throw new HttpException(403);
            }

            if(!$this->validatePhpViewFile()) {
                $this->owner->addError($this->owner->getPageContentProperty(), Yii::t('CustomPagesModule.components_Container', 'Invalid view file selection!'));
            }
        }
    }


    /**
     * Validates the view file setting for php based pages.
     *
     * @return bool
     */
    public function validatePhpViewFile()
    {
        $allowedFiles = $this->getAllowedPhpViewFileSelection();
        return array_key_exists(Html::getAttributeValue($this->owner, $this->owner->getPageContentProperty()), $allowedFiles);
    }

    /**
     * Returns the actual view file path for a php based page.
     *
     * @return bool|null|string
     */
    public function getPhpViewFilePath()
    {
        if($this->isType(self::TYPE_PHP)) {
            $viewFiles = $this->getAllowedPhpViewFileSelection(true);
            $viewName = Html::getAttributeValue($this->owner, $this->owner->getPageContentProperty());

            if(array_key_exists($viewName, $viewFiles)) {
                return $this->getPhpViewPath(basename($viewFiles[$viewName]), true);
            }
        }

        return null;
    }

    public function hasAllowedPhpViews()
    {
        return count($this->getAllowedPhpViewFileSelection()) > 0;
    }

    /**
     * Returns all allowed view files as associative array in the form of
     *
     *  [basename => file path] if $path = true
     *
     * or
     *
     *  [basename => basename] if $path = false
     *
     * @return string[]
     */
    public function getAllowedPhpViewFileSelection($path = false)
    {
        $settings = new SettingsForm();
        if(!$settings->phpPagesActive) {
            return [];
        }

        $files = FileHelper::findFiles($this->getPhpViewPath(), [
            'only' => ['*.php'],
            'recursive' => false
        ]);

        $result = [];
        foreach ($files as $file) {
            $baseName = basename($file, '.php');
            $result[$baseName] = ($path) ? $file : $baseName;
        }

        return $result;
    }

    /**
     * Returns the php view path.
     * @param string $view
     * @param bool $alias
     * @return bool|string
     */
    public function getPhpViewPath($view = '', $alias = false)
    {
        $path = rtrim($this->owner->getPhpViewPath(), '/') . '/'.$view;
        return ($alias) ? $path : Yii::getAlias($path);
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
            Container::TYPE_PHP => Yii::t('CustomPagesModule.base', 'PHP'),
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
            Container::TYPE_TEMPLATE => 'template',
            Container::TYPE_PHP => 'php'
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
