<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\events;

use yii\base\Event;

class DefaultTemplateEvent extends Event
{
    private array $templates = [];

    public function addTemplate(string $name, array $template)
    {
        $this->templates[$name] = $template;
    }

    public function getTemplates(): array
    {
        return $this->templates;
    }
}
