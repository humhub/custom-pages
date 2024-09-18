<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace tests\codeception\fixtures\modules\custom_pages\template;

use yii\test\ActiveFixture;

class PageFixture extends ActiveFixture
{

    public $modelClass = 'humhub\modules\custom_pages\models\Page';
    public $dataFile = '@custom-pages/tests/codeception/fixtures/data/page.php';

    public $depends = [
        ContentFixture::class
    ];

}
