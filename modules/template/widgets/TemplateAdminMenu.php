<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\widgets;

use Yii;
use yii\helpers\Url;

/**
 * Tepmlate AdminMenu
 */
class TemplateAdminMenu extends \humhub\widgets\BaseMenu
{

    /**
     * @inheritdoc
     */
    public $template = "@humhub/widgets/views/subTabMenu";

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->addItem([
            'label' => Yii::t('CustomPagesModule.base', 'Layouts'),
            'url' => Url::to(['/custom_pages/template/layout-admin']),
            'sortOrder' => 100,
            'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'template' && Yii::$app->controller->id == 'layout-admin'),
        ]);
        
        $this->addItem([
            'label' => Yii::t('CustomPagesModule.base', 'Snipped-Layouts'),
            'url' => Url::to(['/custom_pages/template/snipped-layout-admin']),
            'sortOrder' => 200,
            'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'template' && Yii::$app->controller->id == 'snipped-layout-admin' ),
        ]);
        
        $this->addItem([
            'label' => Yii::t('CustomPagesModule.base', 'Containers'),
            'url' => Url::to(['/custom_pages/template/container-admin']),
            'sortOrder' => 300,
            'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'template' && Yii::$app->controller->id == 'container-admin' ),
        ]);
        
        
        parent::init();
    }

}
