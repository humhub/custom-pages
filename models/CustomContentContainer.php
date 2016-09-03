<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace humhub\modules\custom_pages\models;

/**
 *
 * @author buddha
 */
interface CustomContentContainer
{
    public function getContentTypes();
    public function getPageContent();
    public function getAllowedTemplateSelection();
    public function getLabel();
}
