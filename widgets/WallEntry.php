<?php

namespace humhub\modules\custom_pages\widgets;

use humhub\modules\content\widgets\stream\WallStreamModuleEntryWidget;
use humhub\modules\custom_pages\models\CustomContentContainer;

/**
 * Since 0.7.4 there won't be any wallentries for pages and snippets.
 * This file just remains for backward compatibility.
 */
class WallEntry extends WallStreamModuleEntryWidget
{
    /**
     * @var string
     */
    public $editMode = self::EDIT_MODE_NEW_WINDOW;

    /**
     * @var CustomContentContainer
     */
    public $model;

    public function getEditUrl()
    {
        return $this->model->getEditUrl();
    }

    /**
     * @return string returns the content type specific part of this wall entry (e.g. post content)
     */
    protected function renderContent()
    {
        return $this->render('wallEntry', [
            'page' => $this->model,
        ]);
    }

    /**
     * @return string a non encoded plain text title (no html allowed) used in the header of the widget
     */
    protected function getTitle()
    {
        return $this->model->title;
    }
}