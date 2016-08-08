<?php
namespace termsbox\acceptance;


use termsbox\AcceptanceTester;

class TermsboxCest
{
    
    public function testTermsbox(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->wantToTest('the activation of the termsbox');
        $I->amGoingTo('save the termsbox form');
        $I->amOnPage('index-test.php?r=termsbox/admin/index');
        $I->expectTo('See the module configuration page');
        $I->see('Terms Box Configuration');
        
        $I->jsClick('#editform-active'); 
        $I->fillField('EditForm[title]', 'Test title');
        $I->fillField('EditForm[statement]', 'Test statement');
        $I->fillField('EditForm[content]', 'Test content');
        $I->click('Save');
        
        $I->expectTo('see the termsbox');
        $I->wait(2);
        $I->see('Test title');
        $I->see('Test statement');
        $I->see('Test content');
        
        $I->amGoingTo('decline the termsbox');
        $I->click('Decline');
        $I->expectTo('beeing logged out');
        $I->see('Login');
        
       
        
        $I->amGoingTo('login again');
        $I->amAdmin();
        $I->expectTo('see the termsbox again');
        $I->see('Test title');
        $I->see('Test statement');
        $I->see('Test content');
        
        $I->amGoingTo('accept the termsbox');
        $I->click('Accept');
        $I->wait(5);
        $I->expectTo('see no termsbox anymore');
        $I->dontSee('Test title');
        $I->dontSee('Test statement');
        $I->dontSee('Test content');
    }
   
}