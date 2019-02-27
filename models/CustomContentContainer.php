<?php

namespace humhub\modules\custom_pages\models;

use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\custom_pages\interfaces\CustomPagesService;
use humhub\modules\custom_pages\widgets\WallEntry;

/**
 * This abstract class is used by all custom content container types as pages and snippets.
 *
 * Note: Subclasses may container global types not bound to any ContentContainerActiveRecord
 *
 * The followings are the available columns in table 'custom_pages_page':
 * @property integer $id
 * @property integer $type
 * @property string $title
 * @property string $icon
 * @property string $page_content
 * @property integer $sort_order
 * @property integer $admin_only
 * @property integer $in_new_window
 * @property string $target
 * @property string $cssClass
 * @property string $url
 */
abstract class CustomContentContainer extends ContentActiveRecord
{
    /**
     * @inheritdoc
     */
    public $streamChannel = null;

    /**
     * @inheritdoc
     */
    public $autoAddToWall = false;

    /**
     * @inheritdoc
     */
    public $wallEntryClass = WallEntry::class;

    /**
     * @var Target cached target
     */
    private $_target;

    /**
     * @return string
     */
    public abstract function getPageType();

    /**
     * Returns the view url of this page.
     */
    public function getUrl()
    {
        return $this->getTargetModel()->getContentUrl($this);
    }

    /**
     * Returns all allowed content types for a page container class.
     *
     * @return ContentType
     */
    public function getContentType()
    {
        return ContentType::getById($this->type);
    }


    /**
     * @return Target
     */
    public function getTargetModel()
    {
        if(!$this->_target) {
            $this->_target = (new CustomPagesService())->getTargetById($this->getTargetId(), $this->getPageType(), $this->content->container);
        }

        return $this->_target;
    }

    public function render()
    {
        return $this->getContentType()->render($this);
    }

    public function getTargetId()
    {
        return $this->target;
    }

    public function hasTarget($targetId)
    {
        return $this->target === $targetId;
    }

    /**
     * @return string returns the title of this container
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string|null
     */
    public function getIcon() {
        if($this->hasAttribute('icon')) {
            return $this->icon;
        }

        return null;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $this->getContentType()->afterSave($this, $insert, $changedAttributes);
    }


    /**
     * Returns the database content field. Note this does not render the any content.
     */
    public abstract function getPageContent();

    /**
     * Returns the database content field. Note this does not render the any content.
     */
    public function getPageContentProperty() {
        return 'page_content';
    }
    
    /**
     * Returns all allowed templates for a page container class.
     */
    public abstract function getAllowedTemplateSelection();
    
    /**
     * Returns the page container class label.
     * @return string
     */
    public abstract function getLabel();

    /**
     * Returns the view file path for PHP based content.
     * @return string
     */
    public abstract function getPhpViewPath();

    /**
     * @return string
     */
    public abstract function getEditUrl();
}
