<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2023 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace tests\codeception\fixtures\modules\custom_pages\template;

use humhub\modules\content\models\Content;
use yii\test\ActiveFixture;

class ContentFixture extends ActiveFixture
{

    public $modelClass = Content::class;
    public $dataFile = '@custom_pages/tests/codeception/fixtures/data/content.php';

}
