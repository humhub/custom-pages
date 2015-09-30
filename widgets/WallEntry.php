<?php

namespace humhub\modules\custom_pages\widgets;

class WallEntry extends \humhub\modules\content\widgets\WallEntry
{

    public function run()
    {
        return $this->render('wallEntry', array(
                    'page' => $this->contentObject,
        ));
    }

}

?>