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
        $I->waitForElementVisible('[aria-label="Add snippet"]');
        $I->click('[aria-label="Add snippet"]');
        $I->waitForText('Where should it be shown?', 10, '#globalModal');
        $I->click('Dashboard', '#globalModal');

        $I->waitForText('Add new snippet');

        $I->click('#add-content-type-6');

        $I->waitForElementVisible('#custompage-title');

        $I->fillField('CustomPage[title]', 'PHP snippet');
        $I->selectOption('CustomPage[page_content]', ['value' => 'test_snippet']);
        $I->jsClick('.form-collapsible-fields-label.collapsed label');
        $I->scrollToBottom();
        $I->waitForElementVisible('#custompage-sort_order');
        $I->executeJS("$('#custompage-icon').append(new Option('anchor', 'anchor', true, true)).trigger('change');");

        $I->scrollToBottom();
        $I->wait(1);
        $I->click('Create');
        $I->wait(1);

        $I->amOnRoute(['/dashboard/dashboard']);

        $I->expectTo('see no my new page content');

        $I->seeElement('#test-snippet');
        $I->see('My name is: Admin Tester');
    }
}
