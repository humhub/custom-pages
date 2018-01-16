<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace tests\codeception\fixtures\modules\custom_pages\template;

use humhub\modules\custom_pages\modules\template\models\ContainerContentTemplate;
use yii\test\ActiveFixture;

class ContainerContentTemplateFixture extends ActiveFixture
{
    public $modelClass = ContainerContentTemplate::class;
    public $dataFile = '@custom_pages/tests/codeception/fixtures/data/containerContentTemplate.php';
}
