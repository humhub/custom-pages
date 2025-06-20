<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\custom_pages\lib\templates\twig\TwigEngine;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use yii\db\ActiveRecord;

/**
 * @property-read UserElementVariable $author
 * @property-read UserElementVariable $updater
 * @property-read SpaceElementVariable|UserElementVariable $container
 */
class BaseContentRecordElementVariable extends BaseRecordElementVariable
{
    public string $guid;
    public string $createdAt;
    public string $updatedAt;

    public bool $isArchived;
    public bool $isPinned;
    public bool $isHidden;
    public bool $isPublic;
    public bool $isPrivate;
    public bool $lockedComments;

    public bool $isPublished;
    public bool $isDraft;
    public bool $isScheduled;
    public bool $isDeleted;
    public string $url;

    public ?UserElementVariable $_createdBy = null;
    public ?UserElementVariable $_updatedBy = null;
    public SpaceElementVariable|UserElementVariable|null $_container = null;

    public function __construct(BaseElementContent $elementContent)
    {
        parent::__construct($elementContent);
        TwigEngine::registerSandboxExtensionAllowedFunctions(static::class, [
            'getAuthor',
            'getUpdater',
            'getContainer',
        ]);
    }

    public function setRecord(?ActiveRecord $record): BaseRecordElementVariable
    {
        if ($record instanceof ContentActiveRecord) {
            $this->guid = $record->content->guid;
            $this->createdAt = $record->content->created_at;
            $this->updatedAt = $record->content->updated_at;

            $this->isArchived = (bool) $record->content->archived;
            $this->isPinned = (bool) $record->content->pinned;
            $this->isHidden = (bool) $record->content->hidden;
            $this->isPublic = (bool) $record->content->isPublic();
            $this->isPrivate = (bool) $record->content->isPrivate();
            $this->lockedComments = (bool) $record->content->locked_comments;

            $service = $record->content->getStateService();
            $this->isPublished = (bool) $service->isPublished();
            $this->isDraft = (bool) $service->isDraft();
            $this->isScheduled = (bool) $service->isScheduled();
            $this->isDeleted = (bool) $service->isDeleted();

            $this->url = $record->content->getUrl();
        }

        return parent::setRecord($record);
    }

    public function getCreatedBy(): ?UserElementVariable
    {
        if ($this->_createdBy === null && $this->record instanceof ContentActiveRecord) {
            $this->_createdBy = UserElementVariable::instance($this->elementContent)
                ->setRecord($this->record->content->createdBy);
        }

        return $this->_createdBy;
    }

    public function getUpdatedBy(): ?UserElementVariable
    {
        if ($this->_updatedBy === null && $this->record instanceof ContentActiveRecord) {
            $this->_updatedBy = UserElementVariable::instance($this->elementContent)
                ->setRecord($this->record->content->updatedBy);
        }

        return $this->_updatedBy;
    }

    public function getContainer(): UserElementVariable|SpaceElementVariable|null
    {
        if ($this->_container === null && $this->record instanceof ContentActiveRecord) {
            $container = $this->record->content->container;
            if ($container instanceof Space) {
                $this->_container = SpaceElementVariable::instance($this->elementContent);
            } elseif ($container instanceof User) {
                $this->_container = UserElementVariable::instance($this->elementContent);
            } else {
                return null;
            }
            $this->_container->setRecord($this->record->content->container);
        }

        return $this->_container;
    }
}
