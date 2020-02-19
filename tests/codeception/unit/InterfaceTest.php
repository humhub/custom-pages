<?php
namespace tests\codeception\unit\modules\custom_page;

use humhub\modules\custom_pages\interfaces\CustomPagesService;
use humhub\modules\custom_pages\interfaces\CustomPagesTargetEvent;
use humhub\modules\custom_pages\models\HtmlType;
use humhub\modules\custom_pages\models\MarkdownType;
use humhub\modules\custom_pages\models\Page;
use humhub\modules\custom_pages\models\PageType;
use humhub\modules\custom_pages\models\Target;
use humhub\modules\custom_pages\models\TemplateType;
use humhub\modules\space\models\Space;
use tests\codeception\_support\HumHubDbTestCase;
use yii\base\Event;

class InterfaceTest extends HumHubDbTestCase
{
    /**
     * @var CustomPagesService
     */
    public $service;

    public function _before()
    {
        parent::_before();

        $this->service = new CustomPagesService();

        Event::on(CustomPagesService::class, CustomPagesService::EVENT_FETCH_TARGETS, function($event) {
            /* @var $event CustomPagesTargetEvent */
            if($event->container && $event->type === PageType::Page) {
                $event->addTarget(new Target([
                    'id' => 'container',
                    'name' => 'Test Container Target',
                ]));
            }
        });

        Event::on(CustomPagesService::class, CustomPagesService::EVENT_FETCH_TARGETS, function($event) {
            /* @var $event CustomPagesTargetEvent */
            if($event->container && $event->type === PageType::Snippet) {
                $event->addTarget(new Target([
                    'id' => 'containerSnippet',
                    'name' => 'Test Container Target',
                ]));
            }
        });

        Event::on(CustomPagesService::class, CustomPagesService::EVENT_FETCH_TARGETS, function($event) {
            /* @var $event CustomPagesTargetEvent */
            if(!$event->container && $event->type === PageType::Snippet) {
                $event->addTarget(new Target([
                    'id' => 'snippet',
                    'name' => 'Test Container Target',
                ]));
            }
        });

        Event::on(CustomPagesService::class, CustomPagesService::EVENT_FETCH_TARGETS, function($event) {
            /* @var $event CustomPagesTargetEvent */
            if(!$event->container && $event->type === PageType::Page) {
                $event->addTarget(new Target([
                    'id' => 'global',
                    'name' => 'Test Target',
                    'contentTypes' => [MarkdownType::ID, HtmlType::ID]
                ]));
            }
        });

        Event::on(CustomPagesService::class, CustomPagesService::EVENT_FETCH_TARGETS, function($event) {
            /* @var $event CustomPagesTargetEvent */
            if(!$event->container && $event->type === PageType::Page) {
                $event->addTarget(new Target([
                    'id' => 'global2',
                    'name' => 'Test2 Target',
                    'fieldSettings' => [
                        'icon' => false,
                        'admin_only' => false,
                        'sort_order' => false,
                        'cssClass' => false
                    ]
                ]));
            }
        });
    }

    public function testFieldSettings()
    {
        $this->becomeUser('User1');

        $p1 = new Page([
            'title' => 'Test Title',
            'type' => MarkdownType::ID,
            'page_content' => 'Test',
            'target' => 'global2'
        ]);


        $p1->load([
            'Page' => [
                'icon' => 'fa-pencil',
                'visibility' => Page::VISIBILITY_PUBLIC,
                'sort_order' => 300,
                'cssClass' => 'testCss'
            ]
        ]);

        $this->assertTrue($p1->save());

        $page = Page::findOne(['id' => $p1->id]);

        $this->assertNull($page->icon);
        $this->assertNull($page->cssClass);
        $this->assertEquals(0, $page->admin_only);
        $this->assertEquals(0, $page->sort_order);
    }

    public function testFieldSettings2()
    {
        $this->becomeUser('User1');

        $p1 = new Page([
            'title' => 'Test Title',
            'type' => MarkdownType::ID,
            'page_content' => 'Test',
            'target' => 'global'
        ]);


        $p1->load([
            'Page' => [
                'icon' => 'fa-pencil',
                'visibility' => Page::VISIBILITY_ADMIN_ONLY,
                'sort_order' => 300,
                'cssClass' => 'testCss'
            ]
        ]);

        $this->assertTrue($p1->save());

        $page = Page::findOne(['id' => $p1->id]);

        $this->assertEquals('fa-pencil', $page->icon);
        $this->assertEquals('testCss', $page->cssClass);
        $this->assertEquals(1, $page->admin_only);
        $this->assertEquals(300, $page->sort_order);
    }

    public function testTargetAssignment()
    {
        $p1 = new Page([
            'target' => 'global'
        ]);

        $this->assertNotNull($p1->getTargetModel());
        $this->assertEquals('global', $p1->getTargetModel()->id);
    }

    public function testTargetContentValidation()
    {
        $this->becomeUser('User1');

        $p1 = new Page([
            'title' => 'Test Title',
            'type' => MarkdownType::ID,
            'target' => 'global',
            'icon' => 'fa-pencil'
        ]);

        $this->assertFalse($p1->save());

        $p1->page_content = 'Test Content';

        $this->assertTrue($p1->save());
    }

    public function testTargetValidation()
    {
        $this->becomeUser('User1');

        $p1 = new Page(Space::findOne(['id' => 1]), [
            'title' => 'Test Title',
            'type' => MarkdownType::ID,
            'page_content' => 'Test',
            'target' => 'global',
            'icon' => 'fa-pencil'
        ]);

        $this->assertFalse($p1->save());

        $p1->target = 'container';

        $this->assertTrue($p1->save());
    }

    public function testContentTypeValidation()
    {
        $this->becomeUser('User1');

        $p1 = new Page([
            'title' => 'Test Title',
            'type' => TemplateType::ID,
            'page_content' => 'Test',
            'target' => 'global',
            'icon' => 'fa-pencil'
        ]);

        $this->assertFalse($p1->save());

        $p1->type = MarkdownType::ID;

        $this->assertTrue($p1->save());
    }

    public function testAllowedContentType()
    {
        $target = $this->service->getTargetById('global', PageType::Page);
        $this->assertFalse($target->isAllowedContentType(TemplateType::ID));
        $this->assertTrue($target->isAllowedContentType(MarkdownType::ID));
    }

    public function testFindById()
    {
        $globalTarget = $this->service->getTargetById('global', PageType::Page);
        $this->assertNotNull($globalTarget);

        $invalidTarget = $this->service->getTargetById('global', PageType::Snippet);
        $this->assertNull($invalidTarget);

        $invalidTarget = $this->service->getTargetById('global', PageType::Page, Space::findOne(['id' => 1]));
        $this->assertNull($invalidTarget);

        $snippetTarget = $this->service->getTargetById('snippet', PageType::Snippet);
        $this->assertNotNull($snippetTarget);

        $containerSnippetTarget = $this->service->getTargetById('containerSnippet', PageType::Snippet, Space::findOne(['id' => 1]));
        $this->assertNotNull($containerSnippetTarget);

        $invalidTarget = $this->service->getTargetById('snippet', PageType::Snippet, Space::findOne(['id' => 1]));
        $this->assertNull($invalidTarget);
    }

    public function testFindGlobalTargetQuery()
    {
        $targets = $this->service->getTargets(PageType::Page);

        $targetIds = array_map(function($target) {
            return $target['id'];
        }, $targets);

        $this->assertContains('global', $targetIds);
        $this->assertNotContains('container', $targetIds);
        $this->assertNotContains('snippet', $targetIds);
        $this->assertNotContains('containerSnippet', $targetIds);
    }

    public function testGlobalTargetNotPartOfSpaceQuery()
    {
        $targets = $this->service->getTargets(PageType::Page, Space::findOne(['id' => 1]));

        $targetIds = array_map(function($target) {
            return $target['id'];
        }, $targets);

        $this->assertContains('container', $targetIds);
        $this->assertNotContains('global', $targetIds);
        $this->assertNotContains('snippet', $targetIds);
        $this->assertNotContains('containerSnippet', $targetIds);
    }
}