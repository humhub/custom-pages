<?php

namespace custom_pages;

use humhub\modules\content\models\Content;
use humhub\modules\custom_pages\models\CustomPage;
use tests\codeception\_support\HumHubApiTestCest;

/**
 * Base class for the Custom Pages API Cests.
 *
 * The module's shared test fixtures (see @custom_pages/tests/config/test.php) always seed two
 * global Template pages ("test"/"test2") for the template-related unit/functional/acceptance
 * suites - those fixtures are loaded for every suite, including this one, since
 * tests/codeception/_bootstrap.php applies them globally. This base class removes them again
 * right before each API test runs, so every test starts from an empty custom_pages_page table.
 *
 * Note this uses deleteAll(), not TRUNCATE, so it does NOT reset the table's auto-increment
 * counter - ids are not guaranteed to start at 1 and will drift across the suite run (e.g. the
 * two deleted fixture rows alone push the next id to 3). Tests must capture the CustomPage
 * returned by ApiTester::createCustomPage() and assert against its real ->id rather than
 * hardcoding literal ids.
 */
abstract class CustomPagesApiTestCest extends HumHubApiTestCest
{
    public function _before()
    {
        parent::_before();

        Content::deleteAll(['object_model' => CustomPage::class]);
        CustomPage::deleteAll();
    }
}
