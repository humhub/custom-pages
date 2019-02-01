<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

class CreatePhpPageCest
{
    public function testCreateMarkdownPageOnTopMenu(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->amOnPage('index-test.php?r=custom_pages/admin/settings');

        $I->click('[for="phpPagesActive"]');

        $I->wait(1);

        $I->fillField('SettingsForm[phpGlobalPagePath]', '@custom_pages/tests/codeception/_data/views/');
        $I->click('Save');

        $I->seeSuccess('Saved');

        $I->wantToTest('the creation of a php based page');
        $I->amGoingTo('add a new page');
        $I->amOnPage('index-test.php?r=custom_pages/admin/add');
        $I->expectTo('see the add new page site');
        $I->see('Add new page');

        $I->click('#add-6'); // Add Markdown button
        $I->waitForText('Configuration');

        $I->fillField('Page[title]', 'PHP title');
        $I->selectOption('Page[content]', ['value' => 'test_page']);
        $I->selectOption('Page[navigation_class]', 'TopMenuWidget');
        $I->fillField('Page[sort_order]', '400');
        $I->selectOption('Page[icon]',  ['value' => 'fa-adn']);
        $I->click('Save');
        $I->waitForElementVisible('#topbar-second .fa-adn');
        $I->expectTo('see my new page in the top navigation');

        $I->click('#topbar-second .fa-adn');
        $I->expectTo('see no my new page content');

        $I->waitForElementVisible('#test-page');
        $I->see('My name is: Admin Tester');
    }
}