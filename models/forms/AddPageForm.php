<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\custom_pages\models\forms;

use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\custom_pages\models\ContainerPage;
use humhub\modules\custom_pages\models\ContainerSnippet;
use humhub\modules\custom_pages\models\ContentType;
use humhub\modules\custom_pages\models\CustomContentContainer;
use humhub\modules\custom_pages\models\Page;
use humhub\modules\custom_pages\models\PhpType;
use humhub\modules\custom_pages\models\Snippet;
use humhub\modules\custom_pages\models\Target;
use humhub\modules\custom_pages\models\TemplateType;
use Yii;
use yii\base\Model;

/**
 * AddPageForm selects a page type
 *
 * @author luke
 */
class AddPageForm extends Model
{
    /**
     * Defines the target of the page content e.g. Navigation
     * @var Target
     */
    public $target;

    /**
     * Defines the page content type to be created (Markdown,Template,...)
     * @var integer
     */
    public $type;
    
    /**
     * Defines the page type to be created (Page,Snippet,ContainerPage,...).
     * @var string
     */
    public $class;
    
    /**
     * Singleton page instance used for retrieving some page data as the page label.
     * @var mixed
     */
    private $_instance;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['type', 'validateType'],
            [['type', 'target'], 'required'],
        ];
    }

    /**
     * @param $attribute
     * @param $params
     * @throws \yii\base\InvalidConfigException
     */
    public function validateType($attribute, $params)
    {
        if(!$this->isAllowedType($this->type)) {
            $this->addError('type', Yii::t('CustomPagesModule.base', 'Invalid type selection'));
        }
    }

    /**
     * Helper function used by views.
     *
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getPageLabel()
    {
        return $this->getPageInstance()->getLabel();
    }

    /**
     * Tests if the given type is allowed for the given page class.
     *
     * @param integer|ContentType $type
     * @return boolean
     * @throws \yii\base\InvalidConfigException
     */
    public function isAllowedType($type)
    {
        if($type instanceof ContentType) {
            $type = $type->getId();
        }

        if(PhpType::isType($type)) {
            $settings = new SettingsForm();
            if(!$settings->phpPagesActive) {
                return false;
            }
        }

        return in_array($type ,$this->getPageInstance()->getContentTypes()) && $this->target->isAllowedContentType($type);
    }

    /**
     * @param $type
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function isDisabledType($type)
    {
        if($type instanceof ContentType) {
            $type = $type->getId();
        }

        switch ($type) {
            case TemplateType::ID:
                return !$this->showTemplateType();
            case PhpType::ID:
                return !$this->hasPHPFiles();
        }

        return false;
    }

    /**
     * Checks if there are allowed templates available for the given page class.
     *
     * @return boolean
     * @throws \yii\base\InvalidConfigException
     */
    public function showTemplateType()
    {
        return count($this->getPageInstance()->getAllowedTemplateSelection()) > 0;
    }

    /**
     * Returns the singleton page instance.
     *
     * @return CustomContentContainer
     * @throws \yii\base\InvalidConfigException
     */
    public function getPageInstance()
    {
        if($this->_instance == null) {
            $this->_instance = Yii::createObject($this->class);
        }
        return $this->_instance;
    }

    public function hasPHPFiles()
    {
        $settings = new SettingsForm();
        return  $settings->phpPagesActive && $this->getPageInstance()->hasAllowedPhpViews();
    }

    public function getBackUrl()
    {
        switch ($this->class) {
            case Page::class:
            case ContainerPage::class:
                return Url::toPageOverview($this->target->container);
            case Snippet::class:
            case ContainerSnippet::class:
                return Url::toSnippetOverview($this->target->container);
        }

        return null;
    }

}
