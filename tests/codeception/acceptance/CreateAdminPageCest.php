<?php
namespace custom_pages\acceptance;


use custom_pages\AcceptanceTester;

class CreateAdminPageCest
{
    
    public function testCreateMarkdownPageOnTopMenu(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->wantToTest('the creation of a markdown page');
        $I->amGoingTo('add a new page');
        $I->amOnPage('index-test.php?r=custom_pages/admin/add');
        $I->expectTo('see the add new page site');
        $I->see('Add new page');
        
        $I->click('#add-4'); // Add Markdown button
        $I->waitForText('Configuration');
        $I->fillField('Page[title]', 'Test title');
        $I->fillField('Page[content]', 'Test Content');
        $I->selectOption('Page[navigation_class]', 'TopMenuWidget');
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
        $I->wantToTest('the creation of a link page');
        $I->amGoingTo('add a new page');
        $I->amOnPage('index-test.php?r=custom_pages/admin/add');
        $I->expectTo('see the add new page site');
        $I->see('Add new page');
        
        $I->click('#add-1'); // Add link button
        $I->waitForText('Configuration');
        $I->fillField('Page[title]', 'Test link');
        $I->fillField('Page[content]', 'index-test.php?r=dashboard/dashboard');
        $I->selectOption('Page[navigation_class]', 'AccountMenuWidget');
        $I->fillField('Page[sort_order]', '400');
        $I->selectOption('Page[icon]', ['value' => 'fa-adn']);
        $I->click('Save');
        $I->wait(1);
        $I->amOnPage('index-test.php?r=user/account/edit');
        $I->expectTo('see my new page in the user account setting menu');

        $I->waitForElementVisible('.left-navigation .fa-adn');
        $I->see('Test link');
        
        $I->click('.left-navigation .fa-adn');
        $I->expectTo('see the dashboard');
        $I->wait(2);
        $I->seeInCurrentUrl('dashboard/dashboard');
    }
    
    public function testCreateHtmlPage(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->wantToTest('the creation of a html page');
        $I->amGoingTo('add a new page');
        $I->amOnPage('index-test.php?r=custom_pages/admin/add');
        $I->expectTo('see the add new page site');
        $I->waitForText('Add new page');
        
        $I->click('#add-2'); // Add Markdown button
        $I->waitForText('Configuration');
        $I->fillField('Page[title]', 'Test html');
        $I->fillField('Page[content]', '<div id="testDiv">My test div</div>');
        $I->selectOption('Page[navigation_class]', 'TopMenuWidget');
        $I->fillField('Page[sort_order]', '400');
        $I->selectOption('Page[icon]', ['value' => 'fa-adn']);
        $I->click('Save');

        $I->expectTo('see my new page in the top navigation');
        $I->waitForElementVisible('#topbar-second .fa-adn');
        
        $I->click('#topbar-second .fa-adn');
        $I->expectTo('see no my new page content');

        $I->waitForElementVisible('#testDiv');
        $I->see('Test html');
        $I->see('My test div');
    }
   
}