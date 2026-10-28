<?php

namespace custom_pages\acceptance;

use custom_pages\AcceptanceTester;

class CreateGlobalSnippetCest
{
    public function testCreateMarkdownSnippetOnDashboard(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->wantToTest('the creation of a markdown page on Dashboard');
        $I->amGoingTo('add a new page');
        $I->amOnRoute(['/custom_pages/snippet']);
        $I->expectTo('see the add new page site');
        $I->waitForElementVisible('[aria-label="Add snippet"]');
        $I->click('[aria-label="Add snippet"]');
        $I->waitForText('Where should it be shown?', 10, '#globalModal');
        $I->click('Dashboard', '#globalModal');

        $I->waitForText('Add new snippet');
        $I->click('#add-content-type-4');

        $I->waitForElementVisible('#custompage-title');

        $I->fillField('CustomPage[title]', 'Test title');
        $I->fillField('#custompage-page_content .humhub-ui-richtext', 'Test Snippet Content');
        $I->jsClick('.form-collapsible-fields-label.collapsed label');
        $I->scrollToBottom();
        $I->waitForElementVisible('#custompage-sort_order');
        $I->fillField('CustomPage[sort_order]', '400');
        $I->executeJS("$('#custompage-icon').append(new Option('anchor', 'anchor', true, true)).trigger('change');");
        $I->fillField('CustomPage[cssClass]', 'myDashboardWidget');

        $I->scrollToBottom();
        $I->wait(1);
        $I->click('Create');

        $I->wait(1);
        $I->amOnDashboard();

        $I->see('Test title', '.myDashboardWidget');
        $I->see('Test Snippet Content', '.myDashboardWidget');
    }
}
