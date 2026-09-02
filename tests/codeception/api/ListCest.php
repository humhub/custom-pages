<?php

namespace custom_pages\api;

use custom_pages\ApiTester;
use custom_pages\CustomPagesApiTestCest;
use humhub\modules\custom_pages\models\CustomPage;

class ListCest extends CustomPagesApiTestCest
{
    public function testEmptyList(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('see empty custom pages list');
        $I->amAdmin();
        $I->seePaginationCustomPagesResponse('custom-pages', []);
    }

    public function testFilledList(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('see sample created custom pages list');
        $I->amAdmin();
        $page1 = $I->createCustomPage('First global page', 'Sample content for the first global page.');
        $page2 = $I->createCustomPage('Second global page', 'Sample content for the second global page.');
        $I->seePaginationCustomPagesResponse('custom-pages', [$page1->id, $page2->id]);
    }

    public function testFindGlobal(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('see only global custom pages');
        $I->amAdmin();
        $globalPage = $I->createCustomPage('Global page', 'Sample content for a global page.');
        $I->createCustomPage('Space page', 'Sample content for a space page.', ['containerId' => 4]);

        $I->seePaginationCustomPagesResponse('custom-pages/global', [$globalPage->id]);
        $I->seePaginationCustomPagesResponse('custom-pages?contentcontainer_id=0', [$globalPage->id]);
    }

    public function testListByContainer(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('see custom pages by container');
        $I->amAdmin();
        $I->sendGet('custom-pages/container/123');
        $I->seeNotFoundMessage('Content container not found!');

        $page1 = $I->createCustomPage('Sample page title 1', 'Sample page content 1', ['containerId' => 1]);
        $page2 = $I->createCustomPage('Sample page title 2', 'Sample page content 2', ['containerId' => 4]);
        $page3 = $I->createCustomPage('Sample page title 3', 'Sample page content 3', ['containerId' => 6]);
        $page4 = $I->createCustomPage('Sample page title 4', 'Sample page content 4', ['containerId' => 4]);
        $page5 = $I->createCustomPage('Sample page title 5', 'Sample page content 5', ['containerId' => 7]);
        $page6 = $I->createCustomPage('Sample page title 6', 'Sample page content 6', ['containerId' => 4]);

        $I->seePaginationCustomPagesResponse('custom-pages/container/1', [$page1->id]);
        $I->seePaginationCustomPagesResponse('custom-pages/container/4', [$page2->id, $page4->id, $page6->id]);
        $I->seePaginationCustomPagesResponse('custom-pages/container/6', [$page3->id]);
        $I->seePaginationCustomPagesResponse('custom-pages/container/7', [$page5->id]);

        $I->seePaginationCustomPagesResponse('custom-pages?contentcontainer_id=4', [$page2->id, $page4->id, $page6->id]);
    }

    public function testInvalidContentContainerIdParameter(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('see a bad request response for a non-numeric contentcontainer_id');
        $I->amAdmin();
        $I->sendGet('custom-pages?contentcontainer_id=abc');
        $I->seeBadMessage('Invalid contentcontainer_id parameter.');
    }

    public function testAdminSeesAdminOnlyPages(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('see all pages including admin-only ones as an admin');
        $publicPage = $I->createCustomPage('Public page', 'Visible to everyone.', ['visibility' => CustomPage::VISIBILITY_PUBLIC]);
        $adminPage = $I->createCustomPage('Admin-only page', 'Visible to admins only.', ['visibility' => CustomPage::VISIBILITY_ADMIN]);

        $I->amAdmin();
        $I->seePaginationCustomPagesResponse('custom-pages', [$publicPage->id, $adminPage->id]);
    }

    public function testRegularUserDoesNotSeeAdminOnlyPages(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('not see admin-only pages in the list as a regular user');
        $publicPage = $I->createCustomPage('Public page', 'Visible to everyone.', ['visibility' => CustomPage::VISIBILITY_PUBLIC]);
        $I->createCustomPage('Admin-only page', 'Visible to admins only.', ['visibility' => CustomPage::VISIBILITY_ADMIN]);

        // The admin-only page is filtered out of `results`, but `total`/`pages` still reflect the
        // underlying (unfiltered) DB query - see the note on
        // CustomPageController::returnFilteredPagination().
        $I->amUser1();
        $I->seePaginationCustomPagesResponse('custom-pages', [$publicPage->id], ['total' => 2, 'pages' => 1]);
    }
}
