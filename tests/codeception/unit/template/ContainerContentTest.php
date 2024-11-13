<?php

namespace tests\codeception\unit\modules\custom_page\template;

use humhub\modules\custom_pages\modules\template\models\ContainerContentTemplate;
use humhub\modules\custom_pages\modules\template\models\TemplateElement;
use tests\codeception\_support\HumHubDbTestCase;
use humhub\modules\custom_pages\modules\template\models\OwnerContent;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\models\RichtextContent;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\modules\template\models\ContainerContent;
use humhub\modules\custom_pages\modules\template\models\ContainerContentItem;
use humhub\modules\custom_pages\modules\template\models\ContainerContentDefinition;

class ContainerContentTest extends HumHubDbTestCase
{
    public $owner2;
    public $owner1;
    public $page;

    public function testDeleteContainerItem()
    {

        ContainerContentItem::findOne(['id' => 2])->delete();
        ContainerContentItem::findOne(['id' => 3])->delete();
        ContainerContentItem::findOne(['id' => 4])->delete();

        $this->assertNull(OwnerContent::findOne(['id' => 5]));
        $this->assertNull(OwnerContent::findOne(['id' => 6]));
        $this->assertNull(OwnerContent::findOne(['id' => 7]));


        $this->assertNull(RichtextContent::findOne(['id' => 3]));
        $this->assertNull(RichtextContent::findOne(['id' => 4]));
        $this->assertNull(RichtextContent::findOne(['id' => 5]));

    }

    public function testDeleteContainerContent()
    {

        $container = ContainerContent::findOne(['id' => 2]);

        $this->assertEquals(3, $container->getItems()->count());

        $container->delete();

        $this->assertNull(ContainerContentDefinition::findOne(['id' => 2]));

        $this->assertNull(ContainerContentItem::findOne(['id' => 2]));
        $this->assertNull(ContainerContentItem::findOne(['id' => 3]));
        $this->assertNull(ContainerContentItem::findOne(['id' => 4]));

        $this->assertNull(OwnerContent::findOne(['id' => 5]));
        $this->assertNull(OwnerContent::findOne(['id' => 6]));
        $this->assertNull(OwnerContent::findOne(['id' => 7]));


        $this->assertNull(RichtextContent::findOne(['id' => 3]));
        $this->assertNull(RichtextContent::findOne(['id' => 4]));
        $this->assertNull(RichtextContent::findOne(['id' => 5]));

    }

    public function testDeleteParentContainer()
    {

        $container = ContainerContent::findOne(['id' => 1]);

        $container->delete();

        $this->assertNull(ContainerContentItem::findOne(['id' => 2]));
        $this->assertNull(ContainerContentItem::findOne(['id' => 3]));
        $this->assertNull(ContainerContentItem::findOne(['id' => 4]));

        $this->assertNull(OwnerContent::findOne(['id' => 5]));
        $this->assertNull(OwnerContent::findOne(['id' => 6]));
        $this->assertNull(OwnerContent::findOne(['id' => 7]));


        $this->assertNull(RichtextContent::findOne(['id' => 3]));
        $this->assertNull(RichtextContent::findOne(['id' => 4]));
        $this->assertNull(RichtextContent::findOne(['id' => 5]));

    }

    public function testDeletePage()
    {
        $this->becomeUser('Admin');
        $page = CustomPage::find()->where(['custom_pages_page.id' => 2])->readable()->one();

        // Check after soft deletion the Page is not visible even for admin
        $this->assertNotFalse($page->delete());// Soft deletion
        $page = CustomPage::find()->where(['custom_pages_page.id' => 2])->readable()->one();
        $this->assertNull($page);

        $page = CustomPage::findOne(['id' => 2]);
        $this->assertNotFalse($page->hardDelete());

        $this->assertNull(ContainerContentItem::findOne(['id' => 2]));
        $this->assertNull(ContainerContentItem::findOne(['id' => 3]));
        $this->assertNull(ContainerContentItem::findOne(['id' => 4]));

        $this->assertNull(OwnerContent::findOne(['id' => 5]));
        $this->assertNull(OwnerContent::findOne(['id' => 6]));
        $this->assertNull(OwnerContent::findOne(['id' => 7]));

        $this->assertNull(RichtextContent::findOne(['id' => 3]));
        $this->assertNull(RichtextContent::findOne(['id' => 4]));
        $this->assertNull(RichtextContent::findOne(['id' => 5]));
    }

    public function testDeleteAll()
    {
        CustomPage::findOne(['id' => 2])->hardDelete();
        CustomPage::findOne(['id' => 1])->hardDelete();

        $this->assertEquals(0, OwnerContent::find()->where(['not', ['owner_model' => Template::class]])->count());
        $this->assertEquals(0, TemplateInstance::find()->count());
        $this->assertEquals(1, RichtextContent::find()->count());
        $this->assertEquals(0, ContainerContentItem::find()->count());
        $this->assertEquals(0, ContainerContent::find()->count());

        $this->assertEquals(0, ContainerContentDefinition::find()->count());

        $this->assertEquals(1, Template::findOne(['id' => 1])->delete());
        $this->assertEquals(1, Template::findOne(['id' => 2])->delete());

        $this->assertEquals(1, Template::findOne(['id' => 3])->delete());
        $this->assertEquals(1, Template::findOne(['id' => 4])->delete());

        $this->assertEquals(0, RichtextContent::find()->count());
        $this->assertEquals(0, TemplateElement::find()->count());
    }
}
