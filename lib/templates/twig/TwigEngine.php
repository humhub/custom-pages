<?php

namespace humhub\modules\custom_pages\lib\templates\twig;
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use \humhub\modules\custom_pages\lib\templates\TemplateEngine;

/**
 * Description of DatabaseTwigLoader
 *
 * @author buddha
 */
class TwigEngine implements TemplateEngine
{
    public function render($template, $content)
    {
        $loader = new DatabaseTwigLoader();
        $twig = new \Twig_Environment($loader, ['autoescape' => false, 'debug' => true]);
        return $twig->render($template ,$content);
    }

}
