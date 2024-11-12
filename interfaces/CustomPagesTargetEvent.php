<?php

namespace humhub\modules\custom_pages\interfaces;

use humhub\modules\custom_pages\models\PageType;
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

    /**
     * @var array
     */
    private $result = [];

    /**
     * @param $target Target|array
     */
    public function addTarget($target)
    {
        $target = $target instanceof  Target ? $target : new Target($target);
        $target->container = $this->container;

        if (!$target->validate()) {
            Yii::warning('Invalid Custom Pages Navigation given in CustomPagesNavigationEvent::addTarget().');
            return;
        }

        $this->result[$target->id] = $target;
    }

    public function addTargets($targets)
    {
        foreach ($targets as $target) {
            $this->addTarget($target);
        }
    }

    /**
     * @return array
     */
    public function getTargets()
    {
        return $this->result;
    }

    public function addDefaultTargets(): void
    {
        $this->addTargets(PageType::getDefaultTargets($this->type, $this->container));
    }

}
