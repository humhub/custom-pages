<?php
/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 15.02.2019
 * Time: 13:23
 */

namespace humhub\modules\custom_pages\widgets;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\helpers\ContentContainerHelper;
use humhub\modules\custom_pages\helpers\Url;
use \humhub\widgets\BaseMenu;
use Yii;


class OverviewSubMenu extends BaseMenu
{

    /**
     * @inheritdoc
     */
    public $template = "@humhub/widgets/views/subTabMenu";

    /**
     * @var ContentContainerActiveRecord
     */
    public $container;

    public function init()
    {
        $this->container = ContentContainerHelper::getCurrent();

        $this->addItem([
            'label' => Yii::t('CustomPagesModule.base', 'Pages'),
            'url' => Url::toPageOverview($this->container),
            'sortOrder' => 100,
            'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'custom_pages'
                && Yii::$app->controller->id == 'page')
        ]);

        $this->addItem([
            'label' => Yii::t('CustomPagesModule.base', 'Snippets'),
            'url' => Url::toSnippetOverview($this->container),
            'sortOrder' => 100,
            'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'custom_pages'
                && Yii::$app->controller->id == 'snippet')
        ]);
    }

}