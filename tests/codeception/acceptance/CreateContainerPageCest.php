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

        $I->fillField('ContainerPage[title]', 'Space Markdown Page');
        $I->fillField('#containerpage-page_content .humhub-ui-richtext', 'Space Test Content');
        $I->jsShow('.form-collapsible-fields.closed fieldset');
        $I->fillField('ContainerPage[sort_order]', '400');
        $I->selectOption('ContainerPage[icon]',  ['value' => 'fa-adn']);

        $I->click('Save');
        $I->waitForElementVisible('.left-navigation .fa-adn');
        $I->see('Space Markdown Page', '.left-navigation');
        $I->click('Space Markdown Page', '.left-navigation');

        $I->waitForText('Space Test Content');
    }
}