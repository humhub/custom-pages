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
        $I->see('Overview');
        $I->see('Top Navigation');
        $I->seeElement('.target-page-list.TopMenuWidget');

        $I->click('.btn-success', '.target-page-list.TopMenuWidget');

        $I->waitForText('Add new page');
        $I->click('#add-content-type-4');

        $I->waitForText('Configuration');

        $I->fillField('Page[title]', 'Test title');
        $I->fillField('#page-page_content .humhub-ui-richtext', 'Test Content');
        $I->jsShow('.form-collapsible-fields.closed fieldset');
        $I->fillField('Page[sort_order]', '400');
        $I->selectOption('Page[icon]',  ['value' => 'fa-adn']);

        $I->click('Save');
        $I->waitForElementVisible('#topbar-second .fa-adn');
        $I->expectTo('see my new page in the top navigation');

        $I->click('#topbar-second .fa-adn');
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
        $I->see('Overview');
        $I->see('User Account Menu');
        $I->seeElement('.target-page-list.AccountMenuWidget');

        $I->click('.btn-success', '.target-page-list.AccountMenuWidget');

        $I->waitForText('Add new page');
        $I->click('#add-content-type-1');

        $I->waitForText('Configuration');

        $I->fillField('Page[title]', 'Test link');
        $I->fillField('Page[page_content]', '/dashboard/dashboard');
        $I->jsShow('.form-collapsible-fields.closed fieldset');
        $I->fillField('Page[sort_order]', '400');
        $I->selectOption('Page[icon]', ['value' => 'fa-adn']);
        $I->click('Save');
        $I->wait(1);
        $I->amOnRoute(['/user/account/edit']);
        $I->expectTo('see my new page in the user account setting menu');

        $I->waitForElementVisible('.left-navigation .fa-adn');
        $I->see('Test link');

        $I->click('.left-navigation .fa-adn');
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
        $I->see('Overview');
        $I->see('People Buttons');
        $I->seeElement('.target-page-list.PeopleButtonsWidget');

        $I->click('.btn-success', '.target-page-list.PeopleButtonsWidget');

        $I->waitForText('Add new page');
        $I->click('#add-content-type-4');

        $I->waitForText('Configuration');

        $I->fillField('Page[title]', 'Custom people page');
        $I->fillField('#page-page_content .humhub-ui-richtext', 'Custom people page content');
        $I->jsShow('.form-collapsible-fields.closed fieldset');
        $I->fillField('Page[sort_order]', '200');
        $I->selectOption('Page[icon]',  ['value' => 'fa-anchor']);

        $I->click('Save');
        $I->waitForText('People Buttons');
        $I->see('Custom people page');
        $I->amOnRoute('/people');
        $I->waitForElementVisible('.container-people .panel-heading .fa-anchor');
        $I->expectTo('see my new page in the people heading buttons');

        $I->click('.container-people .panel-heading .fa-anchor');
        $I->expectTo('see no my new page content');

        $I->waitForText('Custom people page content');
    }
}