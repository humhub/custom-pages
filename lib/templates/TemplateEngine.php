<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\lib\templates;

/**
 *
 * @author buddha
 */
interface TemplateEngine
{
    /**
     * Renders the content of a given template.
     * 
     * @param string $template template identity
     * @param type $content input content which is used to generate the resulting render output 
     */
    public function render($template, $content);
}
