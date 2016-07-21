<?php

namespace humhub\modules\custom_pages\lib\twig;
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use \humhub\modules\custom_pages\models\Template;

/**
 * Description of DatabaseTwigLoader
 *
 * @author buddha
 */
class DatabaseTwigLoader implements \Twig_LoaderInterface, \Twig_ExistsLoaderInterface
{
    public function exists($name)
    {
        $template = Template::findOne(['name' => $name]);
        return $template != null;
    }

    public function getCacheKey($name)
    {
        return $name;
    }

    public function getSource($name)
    {
        $template = Template::findOne(['name' => $name]);
        if($template == null) {
            throw new Twig_Error_Loader(sprintf('Template "%s" does not exist.', $name));
        }
        return $template->source;
    }

    public function isFresh($name, $time)
    {
         $template = Template::findOne(['name' => $name]);
         if($template == null) {
             return false;
         }
         
         return $template->created_at <= $time;
    }
}
