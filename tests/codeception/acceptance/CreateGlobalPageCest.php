<?php

namespace custom_pages\acceptance;

use custom_pages\AcceptanceTester;

class CreateGlobalPageCest
{
    public function testCreateMarkdownPageOnTopMenu(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->wantToTest('the creation of a markdown page on Top Navigation');
        $I->amGoingTo('add a new page');
        $I->amOnRoute(['/custom_pages/page']);
        $I->expectTo('see the add new page site');
        $I->waitForElementVisible('[aria-label="Add page"]');
        $I->click('[aria-label="Add page"]');
        $I->waitForText('Where should it be shown?', 10, '#globalModal');
        $I->click('Top Navigation', '#globalModal');

        $I->waitForText('Add new page');
        $I->click('#add-content-type-4');

        $I->waitForElementVisible('#custompage-title');

        $I->fillField('CustomPage[title]', 'Test title');
        $I->fillField('#custompage-page_content .humhub-ui-richtext', 'Test Content');
        $I->jsClick('.form-collapsible-fields-label.collapsed label');
        $I->scrollToBottom();
        $I->waitForElementVisible('#custompage-sort_order');
        $I->fillField('CustomPage[sort_order]', '400');
        $I->executeJS("$('#custompage-icon').append(new Option('anchor', 'anchor', true, true)).trigger('change');");

        $I->scrollToBottom();
        $I->wait(1);
        $I->click('Create');
        $I->waitForElementVisible('#topbar-second .ti-anchor');
        $I->expectTo('see my new page in the top navigation');

        $I->click('#topbar-second .ti-anchor');
        $I->expectTo('see no my new page content');

        $I->waitForText('Test Content');
        $I->see('Test title');
    }

    public function testCreateLinkPageOnAccountMenu(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->wantToTest('the creation of a link page on User Account Menu (Settings)');
        $I->amGoingTo('add a new page');
        $I->amOnRoute(['/custom_pages/page']);
        $I->expectTo('see the add new page site');
        $I->waitForElementVisible('[aria-label="Add page"]');
        $I->click('[aria-label="Add page"]');
        $I->waitForText('Where should it be shown?', 10, '#globalModal');
        $I->click('User Account Menu (Settings)', '#globalModal');

        $I->waitForText('Add new page');
        $I->click('#add-content-type-1');

        $I->waitForElementVisible('#custompage-title');

        $I->fillField('CustomPage[title]', 'Test link');
        $I->fillField('CustomPage[page_content]', '/dashboard/dashboard');
        $I->jsClick('.form-collapsible-fields-label.collapsed label');
        $I->scrollToBottom();
        $I->waitForElementVisible('#custompage-sort_order');
        $I->fillField('CustomPage[sort_order]', '400');
        $I->executeJS("$('#custompage-icon').append(new Option('anchor', 'anchor', true, true)).trigger('change');");

        $I->scrollToBottom();
        $I->wait(1);
        $I->click('Create');
        $I->wait(1);
        $I->amOnRoute(['/user/account/edit']);
        $I->expectTo('see my new page in the user account setting menu');

        $I->waitForElementVisible('.left-navigation .ti-anchor');
        $I->see('Test link');

        $I->click('.left-navigation .ti-anchor');
        $I->expectTo('see the dashboard');
        $I->wait(2);
        $I->seeInCurrentUrl('dashboard/dashboard');
    }

    public function testCreateMarkdownPageOnPeopleHeadingButtons(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->wantToTest('the creation of a markdown page on People Heading Buttons');
        $I->amGoingTo('add a new page');
        $I->amOnRoute(['/custom_pages/page']);
        $I->expectTo('see the add new page site');
        $I->waitForElementVisible('[aria-label="Add page"]');
        $I->click('[aria-label="Add page"]');
        $I->waitForText('Where should it be shown?', 10, '#globalModal');
        $I->click('People Buttons', '#globalModal');

        $I->waitForText('Add new page');
        $I->click('#add-content-type-4');

        $I->waitForElementVisible('#custompage-title');

        $I->fillField('CustomPage[title]', 'Custom people page');
        $I->fillField('#custompage-page_content .humhub-ui-richtext', 'Custom people page content');
        $I->jsClick('.form-collapsible-fields-label.collapsed label');
        $I->scrollToBottom();
        $I->waitForElementVisible('#custompage-sort_order');
        $I->fillField('CustomPage[sort_order]', '200');
        $I->executeJS("$('#custompage-icon').append(new Option('anchor', 'anchor', true, true)).trigger('change');");

        $I->scrollToBottom();
        $I->wait(1);
        $I->click('Create');
        $I->scrollToBottom();
        $I->wait(1);
        $I->waitForText('Custom people page');
        $I->see('Custom people page');
        $I->amOnRoute(['/people']);
        $I->waitForElementVisible('.container-people .page-header-actions .ti-anchor');
        $I->expectTo('see my new page in the people heading buttons');

        $I->jsClick('.container-people .page-header-actions .ti-anchor');
        $I->expectTo('see no my new page content');

        $I->waitForText('Custom people page content');
    }
}
