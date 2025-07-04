<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\lib\templates\twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Yii;

class YiiFormaterExtension extends AbstractExtension
{
    /**
     * @inheritdoc
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('asDate', [$this, 'asDate']),
            new TwigFilter('asTime', [$this, 'asTime']),
            new TwigFilter('asDateTime', [$this, 'asDatetime']),
        ];
    }

    public function asDate($date, $format = null): string
    {
        return Yii::$app->formatter->asDate($date, $format);
    }

    public function asTime($date, $format = null): string
    {
        return Yii::$app->formatter->asTime($date, $format);
    }

    public function asDateTime($date, $format = null): string
    {
        return Yii::$app->formatter->asDatetime($date, $format);
    }
}
