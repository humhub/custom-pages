<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use Exception;
use humhub\modules\activity\interfaces\ConfigurableActivityInterface;
use humhub\modules\activity\models\Activity;
use humhub\modules\activity\services\ActivityManager;
use humhub\modules\activity\services\RenderService;
use humhub\modules\custom_pages\lib\templates\twig\TwigEngine;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use Yii;
use yii\db\ActiveRecord;

/**
 * @property ActiveRecord|Activity|null $record
 *
 * @property-read UserElementVariable $createdBy
 * @property-read SpaceElementVariable|UserElementVariable $container
 */
class ActivityElementVariable extends BaseRecordElementVariable
{
    public ?string $title = null;
    public ?string $message = null;
    public ?string $html = null;

    public string $createdAt;

    public ?UserElementVariable $_createdBy = null;
    public SpaceElementVariable|UserElementVariable|null $_container = null;

    public function __construct(BaseElementContent $elementContent)
    {
        parent::__construct($elementContent);
        TwigEngine::registerSandboxExtensionAllowedFunctions(static::class, [
            'getCreatedBy',
            'getContainer',
        ]);
    }

    public function setRecord(?ActiveRecord $record): BaseRecordElementVariable
    {
        if ($record instanceof Activity) {
            $this->createdAt = $record->created_at;

            try {
                $baseActivity = ActivityManager::load($record);

                if ($baseActivity instanceof ConfigurableActivityInterface) {
                    $this->title = $baseActivity->getTitle();
                }

                $renderService = new RenderService($record);

                $this->message = $baseActivity->asHtml();
                $this->html = $renderService->getWeb();
            } catch (Exception $e) {
                Yii::error('Activity not found: ' . $e, 'custom-pages');
            }
        }

        return parent::setRecord($record);
    }
    public function getCreatedBy(): ?UserElementVariable
    {
        if ($this->_createdBy === null && $this->record instanceof Activity) {
            $this->_createdBy = UserElementVariable::instance($this->elementContent)
                ->setRecord($this->record->createdBy);
        }

        return $this->_createdBy;
    }

    public function getContainer(): UserElementVariable|SpaceElementVariable|null
    {
        if ($this->_container === null && $this->record instanceof Activity) {
            $container = $this->record->contentContainer->getPolymorphicRelation();
            if ($container instanceof Space) {
                $this->_container = SpaceElementVariable::instance($this->elementContent);
            } elseif ($container instanceof User) {
                $this->_container = UserElementVariable::instance($this->elementContent);
            } else {
                return null;
            }
            $this->_container->setRecord($container);
        }

        return $this->_container;
    }
}
