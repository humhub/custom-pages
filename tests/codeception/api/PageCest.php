<?php

namespace custom_pages\api;

use custom_pages\ApiTester;
use custom_pages\CustomPagesApiTestCest;
use humhub\modules\custom_pages\models\CustomPage;

class PageCest extends CustomPagesApiTestCest
{
    public function testGetCustomPageById(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('see a custom page by id');
        $I->amAdmin();
        $page = $I->createCustomPage('Sample page title', 'Sample page content');

        $I->sendGet('custom-pages/page/' . $page->id);
        $I->seeCustomPageDefinitionById($page->id);
    }

    public function testGetCustomPageByIdNotFound(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('see a not found response for an unknown custom page id');
        $I->amAdmin();
        $I->sendGet('custom-pages/page/999');
        $I->seeNotFoundMessage('Requested content not found!');
    }

    public function testGetCustomPageByIdAsAdminOnAdminOnlyPage(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('see an admin-only custom page as an admin');
        $page = $I->createCustomPage('Admin-only page', 'Visible to admins only.', [
            'visibility' => CustomPage::VISIBILITY_ADMIN,
        ]);

        $I->amAdmin();
        $I->sendGet('custom-pages/page/' . $page->id);
        $I->seeCustomPageDefinitionById($page->id);
    }

    public function testGetCustomPageByIdForbidden(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('not be allowed to see an admin-only custom page as a regular user');
        $page = $I->createCustomPage('Admin-only page', 'Visible to admins only.', [
            'visibility' => CustomPage::VISIBILITY_ADMIN,
        ]);

        $I->amUser1();
        $I->sendGet('custom-pages/page/' . $page->id);
        $I->seeForbiddenMessage('You cannot view this content!');
    }

    public function testGetCustomPageByIdOnContainer(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('see a space custom page by id');
        $I->amAdmin();
        $page = $I->createCustomPage('Sample space page title', 'Sample space page content', [
            'containerId' => 4,
        ]);

        $I->sendGet('custom-pages/page/' . $page->id);
        $I->seeCustomPageDefinitionById($page->id);
    }
}
