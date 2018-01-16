<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace tests\codeception\fixtures\modules\custom_pages\template;

use yii\test\ActiveFixture;

class SnippetFixture extends ActiveFixture
{
    public $modelClass = 'humhub\modules\custom_pages\models\Snippet';
    public $dataFile = '@custom_pages/tests/codeception/fixtures/data/snippet.php';
}
