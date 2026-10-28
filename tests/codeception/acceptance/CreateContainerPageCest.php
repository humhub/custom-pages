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
        $I->waitForElementVisible('[aria-label="Add page"]');
        $I->click('[aria-label="Add page"]');
        $I->waitForText('Where should it be shown?', 10, '#globalModal');
        $I->click('Space Navigation', '#globalModal');

        $I->waitForText('Add new page');
        $I->click('#add-content-type-4');

        $I->waitForElementVisible('#custompage-title');

        $I->fillField('CustomPage[title]', 'Space Markdown Page');
        $I->fillField('#custompage-page_content .humhub-ui-richtext', 'Space Test Content');
        $I->jsClick('.form-collapsible-fields-label.collapsed label');
        $I->scrollToBottom();
        $I->waitForElementVisible('#custompage-sort_order');
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
