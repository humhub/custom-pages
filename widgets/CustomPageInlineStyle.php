<?php

namespace humhub\modules\custom_pages\widgets;

use humhub\components\Widget;

/**
 * Class CustomPageInlineStyle for custom pages inline styling
 * @package modules\custom_pages\widgets
 */
class CustomPageInlineStyle extends Widget
{
    public function run()
    {
        return $this->render('inline-style');
    }

}
