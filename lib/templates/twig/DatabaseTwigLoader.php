<?php

namespace humhub\modules\custom_pages\lib\templates\twig;
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\custom_pages\modules\template\models\Template;
use Twig\Error\LoaderError;
use Twig\Loader\LoaderInterface;
use Twig\Source;

/**
 * This class is used to load twig templates from the database.
 *
 * @author buddha
 */
class DatabaseTwigLoader implements LoaderInterface
{
    /**
     * @inheritdoc
     */
    public function exists(string $name)
    {
        $template = Template::findOne(['name' => $name, 'cache' => false]);
        return $template != null;
    }

    /**
     * @inheritdoc
     */
    public function getCacheKey(string $name): string
    {
        return $name;
    }

    /**
     * @inheritdoc
     */
    public function getSourceContext(string $name): Source
    {
        $template = Template::findOne(['name' => $name]);
        if($template == null) {
            throw new LoaderError(sprintf('Template "%s" does not exist.', $name));
        }
        return new Source($template->source, $name);
    }

    /**
     * @inheritdoc
     */
    public function isFresh(string $name, int $time): bool
    {
         $template = Template::findOne(['name' => $name]);
         if($template == null) {
             return false;
         }
         
         return $template->created_at <= $time;
    }
}
