<?php

namespace humhub\modules\custom_pages\lib\templates\twig;
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\custom_pages\modules\template\models\Template;

/**
 * This class is used to load twig templates from the database.
 *
 * @author buddha
 */
class DatabaseTwigLoader implements \Twig_LoaderInterface, \Twig_ExistsLoaderInterface
{
    /**
     * @inheritdocs
     */
    public function exists($name)
    {
        $template = Template::findOne(['name' => $name, 'cache' => false]);
        return $template != null;
    }

    /**
     * @inheritdocs
     */
    public function getCacheKey($name)
    {
        return $name;
    }

    /**
     * @inheritdocs
     */
    public function getSource($name)
    {
        $template = Template::findOne(['name' => $name]);
        if($template == null) {
            throw new \Twig_Error_Loader(sprintf('Template "%s" does not exist.', $name));
        }
        return $template->source;
    }

    /**
     * @inheritdocs
     */
    public function isFresh($name, $time)
    {
         $template = Template::findOne(['name' => $name]);
         if($template == null) {
             return false;
         }
         
         return $template->created_at <= $time;
    }
}
