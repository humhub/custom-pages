<?php
namespace custom_pages\acceptance;

use custom_pages\AcceptanceTester;
use humhub\modules\custom_pages\models\Snippet;

class CreateGlobalSnippetCest
{
    
    public function testCreateMarkdownSnippetOnDashboard(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->wantToTest('the creation of a markdown page on Dashboard');
        $I->amGoingTo('add a new page');
        $I->amOnRoute(['/custom_pages/snippet']);
        $I->expectTo('see the add new page site');
        $I->see('Overview');
        $I->see('Dashboard', '.target-page-list');
        $I->seeElement('.target-page-list.'.Snippet::SIDEBAR_DASHBOARD);

        $I->click('.btn-success', '.target-page-list.'.Snippet::SIDEBAR_DASHBOARD);

        $I->waitForText('Add new snippet');
        $I->click('#add-content-type-4');

        $I->waitForText('Configuration');

        $I->fillField('Snippet[title]', 'Test title');
        $I->fillField('#snippet-page_content .humhub-ui-richtext', 'Test Snippet Content');
        $I->jsShow('.form-collapsible-fields.closed fieldset');
        $I->fillField('Snippet[sort_order]', '400');
        $I->selectOption('Snippet[icon]',  ['value' => 'fa-adn']);
        $I->fillField('Snippet[cssClass]',  'myDashboardWidget');

        $I->click('Save');

        $I->wait(1);
        $I->amOnDashboard();

        $I->see('Test title', '.myDashboardWidget');
        $I->see('Test Snippet Content', '.myDashboardWidget');
    }
}