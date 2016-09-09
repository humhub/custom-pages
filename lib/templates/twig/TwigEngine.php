<?php

namespace humhub\modules\custom_pages\lib\templates\twig;
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */


use \humhub\modules\custom_pages\lib\templates\TemplateEngine;

/**
 * The TwigEngine is the default template eninge of this module and is used to
 * initializing the twig environment and rendering templates.
 *
 * @author buddha
 */
class TwigEngine implements TemplateEngine
{
    /**
     * @inerhitdoc
     * 
     * @param type $template template name
     * @param array $content array input [elementName => content]
     * @return string
     */
    public function render($template, $content)
    {
        $loader = new DatabaseTwigLoader();
        $twig = new \Twig_Environment($loader, ['autoescape' => false, 'debug' => true]);
        return $twig->render($template ,$content);
    }

}
