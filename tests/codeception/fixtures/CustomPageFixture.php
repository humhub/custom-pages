<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace tests\codeception\fixtures\modules\custom_pages\template;

use humhub\modules\custom_pages\models\CustomPage;
use yii\test\ActiveFixture;

class CustomPageFixture extends ActiveFixture
{
    public $modelClass = CustomPage::class;
    public $dataFile = '@custom_pages/tests/codeception/fixtures/data/customPage.php';

    public $depends = [
        ContentFixture::class,
    ];

}
