<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace custom_pages\acceptance;

use custom_pages\AcceptanceTester;

class CreatePhpPageCest
{
    public function testCreatePhpPageOnTopMenu(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->amOnRoute(['/custom_pages/config']);

        $I->click('[for="phpPagesActive"]');

        $I->wait(1);

        $I->fillField('SettingsForm[phpGlobalPagePath]', '@custom_pages/tests/codeception/_data/views/global_pages');
        $I->fillField('SettingsForm[phpGlobalSnippetPath]', '@custom_pages/tests/codeception/_data/views/global_snippets');
        $I->fillField('SettingsForm[phpContainerSnippetPath]', '@custom_pages/tests/codeception/_data/views/container_snippets');
        $I->fillField('SettingsForm[phpContainerPagePath]', '@custom_pages/tests/codeception/_data/views/container_pages');
        $I->click('Save');

        $I->seeSuccess('Saved');

        $I->wantToTest('the creation of a php based page');
        $I->amGoingTo('add a new page');

        $I->amOnRoute(['/custom_pages/page']);
        $I->expectTo('see the add new page site');
        $I->see('Overview');
        $I->see('Top Navigation');
        $I->seeElement('.target-page-list.TopMenuWidget');

        $I->click('.btn-success', '.target-page-list.TopMenuWidget');

        $I->waitForText('Add new page');

        $I->click('#add-content-type-6');

        $I->waitForText('Configuration');

        $I->fillField('CustomPage[title]', 'PHP title');
        $I->selectOption('CustomPage[page_content]', ['value' => 'test_page']);
        $I->jsShow('.form-collapsible-fields.closed fieldset');
        $I->fillField('CustomPage[sort_order]', '400');
        $I->selectOption('CustomPage[icon]', ['value' => 'fa-adn']);
        $I->click('Save');
        $I->waitForElementVisible('#topbar-second .fa-adn');
        $I->expectTo('see my new page in the top navigation');

        $I->click('#topbar-second .fa-adn');
        $I->expectTo('see no my new page content');

        $I->waitForElementVisible('#test-page');
        $I->see('My name is: Admin Tester');
    }
}
