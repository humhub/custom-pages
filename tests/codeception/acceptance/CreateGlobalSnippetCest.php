<?php
namespace custom_pages\acceptance;


use custom_pages\AcceptanceTester;
use humhub\modules\custom_pages\models\Snippet;

class CreateGlobalSnippetCest
{
    
    public function testCreateMarkdownSnippetOnDashboard(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->wantToTest('the creation of a markdown page');
        $I->amGoingTo('add a new page');
        $I->amOnPage('index-test.php?r=custom_pages/snippet');
        $I->expectTo('see the add new page site');
        $I->see('Overview');
        $I->see('Dashboard', '.target-page-list');
        $I->see('Directory', '.target-page-list');
        $I->seeElement('.target-page-list.'.Snippet::SIDEBAR_DASHBOARD);

        $I->click('.btn-success', '.target-page-list.'.Snippet::SIDEBAR_DASHBOARD);

        $I->waitForText('Add new snippet');
        $I->click('#add-content-type-4');

        $I->waitForText('Configuration');

        $I->seeInField('input[name="type"][disabled]', 'MarkDown');
        $I->seeInField('input[name="target"][disabled]', 'Dashboard');

        $I->fillField('Snippet[title]', 'Test title');
        $I->fillField('#snippet-page_content .humhub-ui-richtext', 'Test Snippet Content');
        $I->fillField('Snippet[sort_order]', '400');
        $I->selectOption('Snippet[icon]',  ['value' => 'fa-adn']);
        $I->fillField('Snippet[cssClass]',  'myDashboardWidget');

        $I->click('Save');

        $I->wait(1);
        $I->amOnDashboard();

        $I->see('Test title', '.myDashboardWidget');
        $I->see('Test Snippet Content', '.myDashboardWidget');
    }

    public function testCreateIframeSnippetOnDirectory(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->wantToTest('the creation of a markdown page');
        $I->amGoingTo('add a new page');
        $I->amOnPage('index-test.php?r=custom_pages/snippet');
        $I->expectTo('see the add new page site');

        $I->seeElement('.target-page-list.'.Snippet::SIDEBAR_DIRECTORY);

        $I->click('.btn-success', '.target-page-list.'.Snippet::SIDEBAR_DIRECTORY);

        $I->waitForText('Add new snippet');
        $I->click('#add-content-type-3');

        $I->waitForText('Configuration');

        $I->fillField('Snippet[title]', 'Iframe Snippet');
        $I->fillField('Snippet[page_content]', 'https://www.humhub.org');
        $I->selectOption('Snippet[icon]',  ['value' => 'fa-adn']);
        $I->fillField('Snippet[cssClass]',  'myDirectoryWidget');

        $I->click('Save');
        $I->wait(1);
        $I->amOnDirectory();

        $I->see('Iframe Snippet', '.myDirectoryWidget');
        $I->seeElement('.myDirectoryWidget iframe');
    }
}