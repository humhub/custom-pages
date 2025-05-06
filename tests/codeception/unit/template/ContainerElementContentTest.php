<?php

namespace tests\codeception\unit\modules\custom_page\template;

use humhub\modules\custom_pages\modules\template\elements\ContainerDefinition;
use humhub\modules\custom_pages\modules\template\elements\ContainerElement;
use humhub\modules\custom_pages\modules\template\elements\ContainerItem;
use humhub\modules\custom_pages\modules\template\elements\RichtextElement;
use humhub\modules\custom_pages\modules\template\models\TemplateElement;
use tests\codeception\_support\HumHubDbTestCase;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\models\CustomPage;

class ContainerElementContentTest extends HumHubDbTestCase
{
    public function testDeleteContainerItem()
    {
        ContainerItem::findOne(['id' => 2])->delete();
        ContainerItem::findOne(['id' => 3])->delete();
        ContainerItem::findOne(['id' => 4])->delete();

        $this->assertNull(RichtextElement::findOne(['id' => 3]));
        $this->assertNull(RichtextElement::findOne(['id' => 4]));
        $this->assertNull(RichtextElement::findOne(['id' => 5]));
    }

    public function testDeleteContainerElementContent()
    {
        $container = ContainerElement::findOne(['id' => 7]);

        $this->assertEquals(3, $container->getItems()->count());

        $container->delete();

        $this->assertNull(ContainerDefinition::findOne(['id' => 2]));

        $this->assertNull(ContainerItem::findOne(['id' => 2]));
        $this->assertNull(ContainerItem::findOne(['id' => 3]));
        $this->assertNull(ContainerItem::findOne(['id' => 4]));

        $this->assertNull(RichtextElement::findOne(['id' => 3]));
        $this->assertNull(RichtextElement::findOne(['id' => 4]));
        $this->assertNull(RichtextElement::findOne(['id' => 5]));
    }

    public function testDeleteParentContainer()
    {
        $container = ContainerElement::findOne(['id' => 6]);

        $container->delete();

        $this->assertNull(ContainerItem::findOne(['id' => 2]));
        $this->assertNull(ContainerItem::findOne(['id' => 3]));
        $this->assertNull(ContainerItem::findOne(['id' => 4]));

        $this->assertNull(RichtextElement::findOne(['id' => 3]));
        $this->assertNull(RichtextElement::findOne(['id' => 4]));
        $this->assertNull(RichtextElement::findOne(['id' => 5]));
    }

    public function testDeletePage()
    {
        $this->becomeUser('Admin');
        $page = CustomPage::find()->where([CustomPage::tableName() . '.id' => 2])->readable()->one();

        // Check after soft deletion the Page is not visible even for admin
        $this->assertNotFalse($page->delete());// Soft deletion
        $page = CustomPage::find()->where([CustomPage::tableName() . '.id' => 2])->readable()->one();
        $this->assertNull($page);

        $page = CustomPage::findOne(['id' => 2]);
        $this->assertNotFalse($page->hardDelete());

        $this->assertNull(ContainerItem::findOne(['id' => 2]));
        $this->assertNull(ContainerItem::findOne(['id' => 3]));
        $this->assertNull(ContainerItem::findOne(['id' => 4]));

        $this->assertNull(RichtextElement::findOne(['id' => 3]));
        $this->assertNull(RichtextElement::findOne(['id' => 4]));
        $this->assertNull(RichtextElement::findOne(['id' => 5]));
    }

    public function testDeleteAll()
    {
        $this->becomeUser('Admin');
        CustomPage::findOne(['id' => 2])->hardDelete();
        CustomPage::findOne(['id' => 1])->hardDelete();

        $this->assertEquals(0, TemplateInstance::find()->count());
        $this->assertEquals(3, RichtextElement::findByType()->count());
        $this->assertEquals(0, ContainerItem::find()->count());
        $this->assertEquals(0, ContainerElement::findByType()->count());

        $this->assertEquals(1, Template::findOne(['id' => 1])->delete());
        $this->assertEquals(1, Template::findOne(['id' => 2])->delete());

        $this->assertEquals(1, Template::findOne(['id' => 3])->delete());
        $this->assertEquals(1, Template::findOne(['id' => 4])->delete());

        $this->assertEquals(2, RichtextElement::findByType()->count());
        $this->assertEquals(12, TemplateElement::find()->count());

        // Cannot delete default templates
        $this->assertEquals(false, Template::findOne(['is_default' => 1])->delete());
    }
}
