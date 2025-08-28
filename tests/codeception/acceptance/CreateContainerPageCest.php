<?php

namespace custom_pages\acceptance;

use custom_pages\AcceptanceTester;

class CreateContainerPageCest
{
    public function testCreateMarkdownPageOnTopMenu(AcceptanceTester $I)
    {
        $I->amUser1();
        $I->enableModule(2, 'custom_pages');

        $I->wantToTest('the creation of a markdown page');
        $I->amGoingTo('add a new page');
        $I->amOnSpace2('/custom_pages/page');
        $I->expectTo('see the add new page site');
        $I->see('Overview');
        $I->see('Space Navigation');
        $I->seeElement('.target-page-list.SpaceMenu');

        $I->click('.btn-success', '.target-page-list.SpaceMenu');

        $I->waitForText('Add new page');
        $I->click('#add-content-type-4');

        $I->waitForText('Configuration');

        $I->fillField('CustomPage[title]', 'Space Markdown Page');
        $I->fillField('#custompage-page_content .humhub-ui-richtext', 'Space Test Content');
        $I->jsClick('.form-collapsible-fields.closed label');
        $I->fillField('CustomPage[sort_order]', '400');
        $I->selectOption('CustomPage[icon]', ['value' => 'fa-adn']);

        $I->scrollToBottom();
        $I->wait(1);
        $I->click('Create');
        $I->waitForElementVisible('.left-navigation .fa-adn');
        $I->see('Space Markdown Page', '.left-navigation');
        $I->click('Space Markdown Page', '.left-navigation');

        $I->waitForText('Space Test Content');
    }
}
