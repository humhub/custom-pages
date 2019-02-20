<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

class CreatePhpSnippetCest
{
    public function testCreateMarkdownPageOnTopMenu(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->amOnPage('index-test.php?r=custom_pages/config');

        $I->click('[for="phpPagesActive"]');

        $I->wait(1);

        $I->fillField('SettingsForm[phpGlobalSnippetPath]', '@custom_pages/tests/codeception/_data/views/');
        $I->click('Save');

        $I->seeSuccess('Saved');

        $I->wantToTest('the creation of a php based snippet');
        $I->amGoingTo('add a new snippet');
        $I->amOnPage('index-test.php?r=custom_pages/snippet');
        $I->expectTo('see the add new page site');
        $I->see('Overview');

        $I->seeElement('.target-page-list.Dasboard');
        $I->click('.btn-success', '.target-page-list.Dasboard');

        $I->waitForText('Add new snippet');

        $I->click('#add-content-type-6');

        $I->waitForText('Configuration');

        $I->fillField('Snippet[title]', 'PHP snippet');
        $I->selectOption('Snippet[page_content]', ['value' => 'test_snippet']);
        $I->selectOption('Snippet[icon]',  ['value' => 'fa-adn']);
        $I->click('Save');
        $I->wait(1);

        $I->amOnPage('index-test.php?r=dashboard/dashboard');

        $I->expectTo('see no my new page content');

        $I->seeElement('#test-snippet');
        $I->see('My name is: Admin Tester');
    }
}