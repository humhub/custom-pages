<?php

namespace humhub\modules\custom_pages\models;

/**
 * Interface for container classes.
 * 
 * @author buddha
 */
interface CustomContentContainer
{
    /**
     * Returns all allowed content types for a page container class.
     */
    public function getContentTypes();
    
    /**
     * Returns the database content field. Note this does not render the any content.
     */
    public function getPageContent();

    /**
     * Returns the database content field. Note this does not render the any content.
     */
    public function getPageContentProperty();
    
    /**
     * Returns all allowed templates for a page container class.
     */
    public function getAllowedTemplateSelection();
    
    /**
     * Returns the page container class label.
     * @return string
     */
    public function getLabel();

    /**
     * Returns the view file path for PHP based content.
     * @return string
     */
    public function getPhpViewPath();
}
