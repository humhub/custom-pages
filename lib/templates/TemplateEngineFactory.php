<?php

namespace humhub\modules\custom_pages\lib\templates;
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

/**
 * TemplateEngineFactory used to create TemplateEngine instances.
 *
 * @author buddha
 */
class TemplateEngineFactory 
{
    public static function create($engine)
    {
        // Currently the only supported template engin...
        if(strtolower($engine) === 'twig') {
            return new twig\TwigEngine();
        } else {
            return new twig\TwigEngine();
        }
    }
}
