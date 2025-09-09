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
        $I->amOnRoute(['/custom_pages/template/admin']);
        $I->expectTo('see the overview site');
        $I->see('Overview');

        $I->click('Create'); // Add Markdown button

        $I->waitForElementVisible('#template-name');

        $I->fillField('Template[name]', 'MyTestTemplate');
        $I->fillField('Template[description]', 'Test Content');
        $I->jsClick('#template-allow_for_spaces');
        $I->click('Save');

        $I->expectTo('see the edit source view');
        $I->waitForElementVisible('.CodeMirror');

        $I->amGoingTo('add a text element');
        $this->clickAddElement($I, 'Text');
        $I->expectTo('see the add text element view');
        $I->fillField('TemplateElement[name]', 'text');
        $I->fillField('TextElement[content]', 'This is my test text!');
        $I->click('.btn-primary', '#globalModal');
        $I->waitForElementNotVisible('#globalModal');
        $I->expectTo('see the new element added to the source');
        $I->seeInField('#template-form-source', '{{ text }}');

        $I->amGoingTo('add a html element');
        $this->clickAddElement($I, 'Html');
        $I->fillField('TemplateElement[name]', 'html');
        $I->jsFillField('HtmlElement[content]', '<p>Html Test</p>');
        $I->click('.btn-primary', '#globalModal');
        $I->waitForElementNotVisible('#globalModal');

        $I->amGoingTo('add a image element');
        $this->clickAddElement($I, 'Image');
        $I->fillField('TemplateElement[name]', 'tmplimage');
        $I->attachFile('.fileinput-button input[type=file]', 'test.jpg');
        $I->waitForElementVisible('.file-preview-item');
        $I->click('.collapsableTrigger'); //Show more
        $I->waitForElementVisible('#imageelement-definitionpostdata-height');
        $I->fillField('ImageElement[definitionPostData][height]', '100');
        $I->fillField('ImageElement[definitionPostData][width]', '100');
        $I->fillField('ImageElement[definitionPostData][style]', 'border:1px solid black');
        $I->fillField('ImageElement[alt]', 'This is my test alt text');
        $I->click('Save', '#globalModal');
        $I->waitForElementNotVisible('#globalModal');

        $I->amGoingTo('add a file element');
        $this->clickAddElement($I, 'File');
        $I->fillField('TemplateElement[name]', 'file');

        $I->attachFile('files[]', 'test.jpg');
        $I->wait(2);
        $I->click('Save', '#globalModal');
        $I->waitForElementNotVisible('#globalModal');

        $I->click('Save');

        $I->wait(1);

        $I->expectTo('see the new element added to the element row');
        $I->see('{{ text }}', '#templateElements');
        $I->see('{{ html }}', '#templateElements');
        $I->see('{{ tmplimage }}', '#templateElements');
        $I->see('{{ file }}', '#templateElements');
    }

    private function clickAddElement(AcceptanceTester $I, $type)
    {
        $I->click('Add Element');
        $I->wait(1);
        $I->click($type);
        $I->waitForElementVisible('#templateelement-name');
    }
}
