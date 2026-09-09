<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace custom_pages\functional;

use custom_pages\FunctionalTester;
use PHPUnit\Framework\Assert;

class EditMultipleElementsCest
{
    public function testContainerItemsInEditDialog(FunctionalTester $I)
    {
        $I->wantTo('see the items of the containers in the edit dialog of an existing template instance');
        $I->amAdmin();

        // Template instance 3 uses the template "containerText" with a multiple container holding three items
        $I->amOnRoute('/custom_pages/template/element-content/edit-multiple', ['id' => 3]);
        $output = json_decode($I->grabPageSource(), true)['output'] ?? '';
        Assert::assertStringContainsString('cp-container-items', $output);
        Assert::assertSame(3, substr_count($output, 'data-item-id='));
        Assert::assertStringContainsString('simpleText', $output);
        Assert::assertStringNotContainsString('This section has no editable elements', $output);

        // The layout instance 2 has only a container with a single item
        $I->amOnRoute('/custom_pages/template/element-content/edit-multiple', ['id' => 2]);
        $output = json_decode($I->grabPageSource(), true)['output'] ?? '';
        Assert::assertStringContainsString('cp-container-items', $output);
        Assert::assertSame(1, substr_count($output, 'data-item-id='));
        Assert::assertStringNotContainsString('This section has no editable elements', $output);
    }

    public function testNoContainerItemsWhenAddingItem(FunctionalTester $I)
    {
        $I->wantTo('not see any container items while adding a new item');
        $I->amAdmin();

        // Add a new "containerText" item (template with a container) to the multiple container content 7
        $I->amOnRoute('/custom_pages/template/container-content/edit-add-item', ['elementContentId' => 7, 'templateId' => 3]);
        $output = json_decode($I->grabPageSource(), true)['output'] ?? '';
        Assert::assertNotEmpty($output);
        Assert::assertStringNotContainsString('cp-container-items', $output);
    }
}
