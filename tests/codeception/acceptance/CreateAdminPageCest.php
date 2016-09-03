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
        $I->fillField('Page[title]', 'Test title');
        $I->fillField('Page[content]', 'Test Content');
        $I->selectOption('Page[navigation_class]', 'TopMenuWidget');
        $I->fillField('Page[sort_order]', '400');
        $I->selectOption('Page[icon]', 'fa-adn');
        $I->click('Save');
        $I->wait(3);
        $I->expectTo('see my new page in the top navigation');
        $I->seeElement('#topbar-second .fa-adn');
        
        $I->click('#topbar-second .fa-adn');
        $I->expectTo('see no my new page content');
        $I->see('Test title');
        $I->see('Test Content');
    }
    
    public function testCreateLinkPageOnAccountMenu(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->wantToTest('the creation of a markdown page');
        $I->amGoingTo('add a new page');
        $I->amOnPage('index-test.php?r=custom_pages/admin/add');
        $I->expectTo('see the add new page site');
        $I->see('Add new page');
        
        $I->click('#add-1'); // Add link button
        $I->fillField('Page[title]', 'Test link');
        $I->fillField('Page[content]', 'index-test.php?r=dashboard/dashboard');
        $I->selectOption('Page[navigation_class]', 'AccountMenuWidget');
        $I->fillField('Page[sort_order]', '400');
        $I->selectOption('Page[icon]', 'fa-adn');
        $I->click('Save');
        $I->wait(3);
        
        $I->amOnPage('index-test.php?r=user/account/edit');
        $I->expectTo('see my new page in the user account setting menu');
        $I->seeElement('.container .col-md-2 .fa-adn');
        $I->see('Test link');
        
        $I->click('.container .col-md-2 .fa-adn');
        $I->expectTo('see the dashboard');
        $I->seeInCurrentUrl('dashboard/dashboard');
    }
    
    public function testCreateHtmlPage(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->wantToTest('the creation of a markdown page');
        $I->amGoingTo('add a new page');
        $I->amOnPage('index-test.php?r=custom_pages/admin/add');
        $I->expectTo('see the add new page site');
        $I->see('Add new page');
        
        $I->click('#add-2'); // Add Markdown button
        $I->fillField('Page[title]', 'Test html');
        $I->fillField('Page[content]', '<div id="testDiv">My test div</div>');
        $I->selectOption('Page[navigation_class]', 'TopMenuWidget');
        $I->fillField('Page[sort_order]', '400');
        $I->selectOption('Page[icon]', 'fa-adn');
        $I->click('Save');
        $I->wait(3);
        $I->expectTo('see my new page in the top navigation');
        $I->seeElement('#topbar-second .fa-adn');
        
        $I->click('#topbar-second .fa-adn');
        $I->expectTo('see no my new page content');
        $I->see('Test html');
        $I->seeElement('#testDiv');
        $I->see('My test div');
    }
   
}