<?php

namespace humhub\modules\custom_pages\modules\template\services;

use humhub\modules\custom_pages\modules\template\elements\BaseElementContent;
use humhub\modules\custom_pages\modules\template\elements\ContainerElement;
use humhub\modules\custom_pages\modules\template\elements\FileDownloadElement;
use humhub\modules\custom_pages\modules\template\elements\FileElement;
use humhub\modules\custom_pages\modules\template\elements\HumHubRichtextElement;
use humhub\modules\custom_pages\modules\template\elements\ImageElement;
use humhub\modules\custom_pages\modules\template\elements\RichtextElement;
use humhub\modules\custom_pages\modules\template\elements\RssElement;
use humhub\modules\custom_pages\modules\template\elements\SpaceElement;
use humhub\modules\custom_pages\modules\template\elements\SpacesElement;
use humhub\modules\custom_pages\modules\template\elements\TextElement;
use humhub\modules\custom_pages\modules\template\elements\UserElement;
use humhub\modules\custom_pages\modules\template\elements\UsersElement;
use yii\base\Component;

/**
 * ElementTypeService provides a list of available element types.
 *
 * Module API:
 *
 * Inject own element types via Module Event.
 *
 * `config.php`:
 *
 * ```php
 *  [
 *      '\humhub\modules\custom_pages\modules\template\services\ElementTypeService', 'init',
 *      [Events::class, 'onCustomPagesTemplateElementTypeServiceInit']
 *  ],
 * ```
 * `Events.php`:
 *
 * ```php
 *      public static function onCustomPagesTemplateElementAvailableTypes(Event $event)
 *      {
 *         $elementTypeService = $event->sender;
 *         $elementTypeService->addType(CalendarsElement::class);
 *      }
 *  }
 *  ```
 */
final class ElementTypeService extends Component
{
    private const DEFAULT_TYPES = [
        TextElement::class,
        RichtextElement::class,
        HumHubRichtextElement::class,
        ImageElement::class,
        FileElement::class,
        FileDownloadElement::class,
        ContainerElement::class,
        RssElement::class,
        UserElement::class,
        SpaceElement::class,
        UsersElement::class,
        SpacesElement::class,
    ];

    private array $types = [];

    public const EVENT_INIT = 'init';

    public function init()
    {
        $this->types = self::DEFAULT_TYPES;
        $this->trigger(self::EVENT_INIT);
    }

    public function addType(string $type): void
    {
        $this->types[] = $type;
    }

    /**
     * @return BaseElementContent[]
     */
    public function getTypeInstances(): array
    {
        $instances = [];
        foreach ($this->types as $type) {
            $instances[] = new $type();
        }
        return $instances;
    }

}
