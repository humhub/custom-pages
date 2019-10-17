<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace custom_pages\functional;

use humhub\modules\custom_pages\interfaces\CustomPagesService;
use humhub\modules\custom_pages\interfaces\CustomPagesTargetEvent;
use humhub\modules\custom_pages\models\PageType;
use humhub\modules\custom_pages\models\Target;
use custom_pages\FunctionalTester;
use yii\base\Event;

class InterfaceCest
{

    /**
     * @param FunctionalTester $I
     */
    public function testTarget(FunctionalTester $I)
    {
        $I->wantTo('make sure users without create permission can\'t create pages');

        Event::on(CustomPagesService::class, CustomPagesService::EVENT_FETCH_TARGETS, function($event) {

           /* @var $event CustomPagesTargetEvent */

            if(!$event->container && $event->type === PageType::Page) {
                $event->addTarget(new Target([
                    'id' => 'test',
                    'name' => 'Test Target',
                    'icon' => 'fa-bath',
                ]));
            }

        });

        $I->amAdmin();

        $I->amOnRoute('/custom_pages/page');
        $I->see('Test Target', '.target-page-list');


        $I->enableModule(1, 'custom_pages');
        $I->amOnSpace1('/custom_pages/page');
        $I->see('Space Navigation');
        $I->dontSee('Test Target');
        $I->dontSeeElement('.fa-bath');
    }
}
