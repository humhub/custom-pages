<?php

namespace humhub\modules\custom_pages\interfaces;

use humhub\modules\custom_pages\helpers\PageType;
use humhub\modules\custom_pages\models\Target;
use Yii;

/**
 * Class CustomPagesNavigationEvent
 * @package humhub\modules\custom_pages\interfaces
 */
class CustomPagesTargetEvent extends CustomPagesEvent
{
    /**
     * @var string
     */
    public $type;

    private array $result = [];

    /**
     * @param Target|array $target
     */
    public function addTarget($target): void
    {
        $target = $target instanceof  Target ? $target : new Target($target);
        $target->container = $this->container;

        if (!$target->validate()) {
            Yii::warning('Invalid Custom Pages Navigation given in CustomPagesNavigationEvent::addTarget().');
            return;
        }

        $this->result[$target->id] = $target;
    }

    public function addTargets($targets): void
    {
        foreach ($targets as $id => $target) {
            if (is_string($target)) {
                $target = ['name' => $target];
            }
            if (!isset($target['id'])) {
                $target['id'] = $id;
            }
            $this->addTarget($target);
        }
    }

    /**
     * @return array
     */
    public function getTargets(): array
    {
        return $this->result;
    }

    public function addDefaultTargets(): void
    {
        $this->addTargets(PageType::getDefaultTargets($this->type, $this->container));
    }

}
