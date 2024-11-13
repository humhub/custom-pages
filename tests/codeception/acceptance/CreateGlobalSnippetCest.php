<?php

namespace custom_pages\acceptance;

use custom_pages\AcceptanceTester;
use humhub\modules\custom_pages\helpers\PageType;

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
        $I->seeElement('.target-page-list.' . PageType::TARGET_DASHBOARD_SIDEBAR);

        $I->click('.btn-success', '.target-page-list.' . PageType::TARGET_DASHBOARD_SIDEBAR);

        $I->waitForText('Add new snippet');
        $I->click('#add-content-type-4');

        $I->waitForText('Configuration');

        $I->fillField('CustomPage[title]', 'Test title');
        $I->fillField('#snippet-page_content .humhub-ui-richtext', 'Test Snippet Content');
        $I->jsShow('.form-collapsible-fields.closed fieldset');
        $I->fillField('CustomPage[sort_order]', '400');
        $I->selectOption('CustomPage[icon]', ['value' => 'fa-adn']);
        $I->fillField('CustomPage[cssClass]', 'myDashboardWidget');

        $I->click('Save');

        $I->wait(1);
        $I->amOnDashboard();

        $I->see('Test title', '.myDashboardWidget');
        $I->see('Test Snippet Content', '.myDashboardWidget');
    }
}
