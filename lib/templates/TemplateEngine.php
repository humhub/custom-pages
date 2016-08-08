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
    public function render($template, $content);
}
