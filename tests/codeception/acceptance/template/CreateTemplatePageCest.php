<?php
namespace custom_pages\acceptance\template;


use custom_pages\AcceptanceTester;

class CreateTemplatePageCest
{
    
    public function testCreateTemplate(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->wantToTest('the creation of a template page');
        $I->amGoingTo('add a new layout template');
        $I->amOnPage('index-test.php?r=custom_pages/template/layout-admin');
        $I->expectTo('see the overview site');
        $I->see('Overview');
        
        $I->click('Create new layout'); // Add Markdown button

        $I->waitForElementVisible('#template-name');

        $I->fillField('Template[name]', 'MyTestTemplate');
        $I->fillField('Template[description]', 'Test Content');
        $I->jsClick('#template-allow_for_spaces');
        $I->click('Save');
        
        $I->expectTo('see the edit source view');
        $I->waitForElementVisible('#template-form-source');
        
        $I->amGoingTo('add a text element');
        $this->clickAddElement($I, 'Text');
        $I->expectTo('see the add text element view');
        $I->fillField('TemplateElement[name]', 'text');
        $I->fillField('TextContent[content]', 'This is my test text!');
        $I->click('.btn-primary', '#globalModal');
        $I->waitForElementNotVisible('#globalModal');
        $I->expectTo('see the new element added to the source');
        $I->seeInField('#template-form-source', '{{ text }}');
        
        $I->amGoingTo('add a richtext element');
        $this->clickAddElement($I, 'Richtext');
        $I->fillField('TemplateElement[name]', 'richtext');
        $I->jsFillField('RichtextContent[content]', '<p>Richtext Test</p>');
        $I->click('.btn-primary', '#globalModal');
        $I->waitForElementNotVisible('#globalModal');
        
        $I->amGoingTo('add a image element');
        $this->clickAddElement($I, 'Image');
        $I->fillField('TemplateElement[name]', 'image');
        //Workaround
        $I->jsShow('.uploadElementImage', 'type');
        #$I->wait(20);
        $I->attachFile('files[]', 'test.jpg');
        $I->click('.collapsableTrigger'); //Show more
        $I->wait(2);
        $I->fillField('ImageContent[definitionPostData][height]', '100');
        $I->fillField('ImageContent[definitionPostData][width]', '100');
        $I->fillField('ImageContent[definitionPostData][style]', 'border:1px solid black');
        $I->fillField('ImageContent[alt]', 'This is my test alt text');
        $I->click('Save', '#globalModal');
        $I->waitForElementNotVisible('#globalModal');
        
        $I->amGoingTo('add a file element');
        $this->clickAddElement($I, 'File');
        $I->fillField('TemplateElement[name]', 'file');
        
        //Workaround
        $I->jsShow('.uploadElementImage', 'type');

        $I->attachFile('files[]', 'test.jpg');
        $I->wait(2);
        $I->click('Save', '#globalModal');
        $I->waitForElementNotVisible('#globalModal');

        $I->click('Save');
       
        $I->expectTo('see the new element added to the element row');
        $I->see('#text');
        $I->see('#richtext');
        $I->see('#image');
        $I->see('#file');
    }
    
    private function clickAddElement($I, $type) {
        $I->click('Add Element');
        $I->wait(1);
        
        $I->click($type);
        $I->waitForElementVisible('#templateelement-name');
        
    }
}