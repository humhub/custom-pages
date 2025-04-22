<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\events;

use humhub\modules\custom_pages\modules\template\elements\ContainerElement;
use humhub\modules\custom_pages\modules\template\models\Template;

class DefaultTemplateEvents
{
    public static function onImportServiceFetchTemplates(DefaultTemplateEvent $event)
    {
        $event->addTemplate('system_one_column_layout', [
            'type' => Template::TYPE_LAYOUT,
            'description' => 'Simple one column layout.',
            'source' => '<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-body">
                {{ content }}
            </div>
        </div>
    </div>
</div>',
            'allow_for_spaces' => false,
            'elements' => [
                [
                    'name' => 'content',
                    'content_type' => ContainerElement::class,
                    'dyn_attributes' => [
                        'allow_multiple' => 1,
                        'is_inline' => 0,
                        'templates' => [],
                    ],
                ],
            ],
        ]);
    }
}
