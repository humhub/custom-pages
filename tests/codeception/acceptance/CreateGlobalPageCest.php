<?php
namespace custom_pages\acceptance;


use custom_pages\AcceptanceTester;

class CreateGlobalPageCest
{
    
    public function testCreateMarkdownPageOnTopMenu(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->wantToTest('the creation of a markdown page');
        $I->amGoingTo('add a new page');
        $I->amOnPage('index-test.php?r=custom_pages/page');
        $I->expectTo('see the add new page site');
        $I->see('Overview');
        $I->see('Top Navigation');
        $I->seeElement('.target-page-list.TopMenuWidget');

        $I->click('.btn-success', '.target-page-list.TopMenuWidget');

        $I->waitForText('Add new page');
        $I->click('#add-content-type-4');

        $I->waitForText('Configuration');

        $I->seeInField('input[name="type"][disabled]', 'MarkDown');
        $I->seeInField('input[name="target"][disabled]', 'Top Navigation');

        $I->fillField('Page[title]', 'Test title');
        $I->fillField('#page-page_content .humhub-ui-richtext', 'Test Content');
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
        $I->amOnPage('index-test.php?r=custom_pages/page');
        $I->expectTo('see the add new page site');
        $I->see('Overview');
        $I->see('User Account Menu');
        $I->seeElement('.target-page-list.AccountMenuWidget');

        $I->click('.btn-success', '.target-page-list.AccountMenuWidget');

        $I->waitForText('Add new page');
        $I->click('#add-content-type-1');

        $I->waitForText('Configuration');

        $I->seeInField('input[name="type"][disabled]', 'Link');
        $I->seeInField('input[name="target"][disabled]', 'User Account Menu (Settings)');

        $I->fillField('Page[title]', 'Test link');
        $I->fillField('Page[page_content]', 'index-test.php?r=dashboard/dashboard');
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
    
    public function testCreateHtmlPageOnDirectoryMenu(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->wantToTest('the creation of a html page');
        $I->amGoingTo('add a new page');
        $I->amOnPage('index-test.php?r=custom_pages/page');
        $I->expectTo('see the add new page site');
        $I->see('Overview');
        $I->see('Directory Menu');
        $I->seeElement('.target-page-list.DirectoryMenu');

        $I->click('.btn-success', '.target-page-list.DirectoryMenu');

        $I->waitForText('Add new page');
        $I->click('#add-content-type-2');

        $I->waitForText('Configuration');

        $I->seeInField('input[name="type"][disabled]', 'Html');
        $I->seeInField('input[name="target"][disabled]', 'Directory Menu');

        $I->fillField('Page[title]', 'Test html');

        $I->executeJS('$(".CodeMirror:visible")[0].CodeMirror.getDoc().setValue("<div id=\"testDiv\">My test div</div>")');
        $I->executeJS('$(".CodeMirror:visible")[0].CodeMirror.save()');

        $I->fillField('Page[sort_order]', '400');
        $I->selectOption('Page[icon]', ['value' => 'fa-adn']);
        $I->click('Save');

        $I->waitForText('Overview');
        $I->wait(1);

        $I->amOnDirectory();

        $I->see('Test html', '.left-navigation');
        $I->click('Test html', '.left-navigation');

        $I->waitForText('My test div');
        $I->seeElement('#testDiv');
    }
   
}