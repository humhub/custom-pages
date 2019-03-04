<?php

namespace humhub\modules\custom_pages\widgets;

use humhub\modules\content\widgets\WallEntry as BaseWallEntry;
use humhub\modules\custom_pages\models\CustomContentContainer;

/**
 * Since 0.7.4 there won't be any wallentries for pages and snippets.
 * This file just remains for backward compatibility.
 */
class WallEntry extends BaseWallEntry
{
    public $editMode = self::EDIT_MODE_NEW_WINDOW;

    /**
     * @var CustomContentContainer
     */
    public $contentObject;

    public function getEditUrl()
    {
        return $this->contentObject->getEditUrl();
    }

    public function run()
    {
        return $this->render('wallEntry', [
            'page' => $this->contentObject,
        ]);
    }

}