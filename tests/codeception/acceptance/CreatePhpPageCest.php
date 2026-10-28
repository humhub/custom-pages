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
        $I->waitForElementVisible('[aria-label="Add page"]');
        $I->click('[aria-label="Add page"]');
        $I->waitForText('Where should it be shown?', 10, '#globalModal');
        $I->click('Top Navigation', '#globalModal');

        $I->waitForText('Add new page');

        $I->click('#add-content-type-6');

        $I->waitForElementVisible('#custompage-title');

        $I->fillField('CustomPage[title]', 'PHP title');
        $I->selectOption('CustomPage[page_content]', ['value' => 'test_page']);
        $I->jsClick('.form-collapsible-fields-label.collapsed label');
        $I->scrollToBottom();
        $I->waitForElementVisible('#custompage-sort_order');
        $I->fillField('CustomPage[sort_order]', '400');
        $I->selectOption('CustomPage[icon]', ['value' => 'fa-anchor']);

        $I->scrollToBottom();
        $I->wait(1);
        $I->click('Create');
        $I->waitForElementVisible('#topbar-second .ti-anchor');
        $I->expectTo('see my new page in the top navigation');

        $I->click('#topbar-second .ti-anchor');
        $I->expectTo('see no my new page content');

        $I->waitForElementVisible('#test-page');
        $I->see('My name is: Admin Tester');
    }
}
