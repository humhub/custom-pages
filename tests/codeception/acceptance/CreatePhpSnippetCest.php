<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace custom_pages\acceptance;

use custom_pages\AcceptanceTester;

class CreatePhpSnippetCest
{
    public function testCreateMarkdownPageOnTopMenu(AcceptanceTester $I)
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

        $I->wantToTest('the creation of a php based snippet');
        $I->amGoingTo('add a new snippet');
        $I->amOnRoute(['/custom_pages/snippet']);
        $I->expectTo('see the add new page site');
        $I->see('Overview');

        $I->seeElement('.target-page-list.Dashboard');
        $I->click('.btn-success', '.target-page-list.Dashboard');

        $I->waitForText('Add new snippet');

        $I->click('#add-content-type-6');

        $I->waitForText('Configuration');

        $I->fillField('Snippet[title]', 'PHP snippet');
        $I->selectOption('Snippet[page_content]', ['value' => 'test_snippet']);
        $I->jsShow('.form-collapsible-fields.closed fieldset');
        $I->selectOption('Snippet[icon]', ['value' => 'fa-adn']);
        $I->click('Save');
        $I->wait(1);

        $I->amOnRoute(['/dashboard/dashboard']);

        $I->expectTo('see no my new page content');

        $I->seeElement('#test-snippet');
        $I->see('My name is: Admin Tester');
    }
}
