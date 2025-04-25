<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\events;

use yii\base\Event;

class DefaultTemplateEvent extends Event
{
    /**
     * @var array Paths with json files of the default templates
     */
    private array $paths = [];

    public function addPath(string $path): void
    {
        $this->paths[] = $path;
    }

    public function getPaths(): array
    {
        return $this->paths;
    }
}
