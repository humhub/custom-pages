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
        $I->amOnPage('index-test.php?r=custom_pages/config');

        $I->click('[for="phpPagesActive"]');

        $I->wait(1);

        $I->fillField('SettingsForm[phpGlobalPagePath]', '@custom_pages/tests/codeception/_data/views/');
        $I->click('Save');

        $I->seeSuccess('Saved');

        $I->wantToTest('the creation of a php based page');
        $I->amGoingTo('add a new page');

        $I->amOnPage('index-test.php?r=custom_pages/page');
        $I->expectTo('see the add new page site');
        $I->see('Overview');
        $I->see('Top Navigation');
        $I->seeElement('.target-page-list.TopMenuWidget');

        $I->click('.btn-success', '.target-page-list.TopMenuWidget');

        $I->waitForText('Add new page');

        $I->click('#add-content-type-6');

        $I->waitForText('Configuration');

        $I->seeInField('input[name="type"][disabled]', 'PHP');
        $I->seeInField('input[name="target"][disabled]', 'Top Navigation');

        $I->fillField('Page[title]', 'PHP title');
        $I->selectOption('Page[page_content]', ['value' => 'test_page']);
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