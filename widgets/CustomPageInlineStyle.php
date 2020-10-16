<?php


namespace humhub\modules\custom_pages\widgets;

use humhub\components\Widget;
use humhub\modules\ui\view\components\Theme;

/**
 * Class CustomPageInlineStyle for custom pages inline styling
 * @package modules\custom_pages\widgets
 */
class CustomPageInlineStyle extends Widget
{
    /**
     * @var Theme
     */
    public $theme;

    public function run()
    {
        $linkColor = $this->theme->variable('link', $this->theme->variable('info'));
        return $this->render('inline-style', [
            'linkColor' => $linkColor,
        ]);
    }

}
