<?php

namespace humhub\modules\custom_pages\widgets;

/**
 * Since 0.7.4 there won't be any wallentries for pages and snippets.
 * This file just remains for backward compatibility. 
 * Note: Before removing this file the $streamChannel for snippets and container pages has to be set to null!
 * 
 * @deprecated since version 0.7.4
 */
class WallEntry extends \humhub\modules\content\widgets\WallEntry
{

    public function run()
    {
        return $this->render('wallEntry', array(
                    'page' => $this->contentObject,
        ));
    }

}