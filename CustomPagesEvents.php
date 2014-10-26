<?php

/**
 * HumHub
 * Copyright © 2014 The HumHub Project
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 */

/**
 * Description of CustomPagesEvents
 *
 * @author luke
 */
class CustomPagesEvents
{

    public static function onAdminMenuInit($event)
    {
        $event->sender->addItem(array(
            'label' => Yii::t('CustomPagesModule.base', 'Custom Pages'),
            'url' => Yii::app()->createUrl('//custom_pages/admin'),
            'group' => 'manage',
            'icon' => '<i class="fa fa-file-o"></i>',
            'isActive' => (Yii::app()->controller->module && Yii::app()->controller->module->id == 'custom_pages' && Yii::app()->controller->id == 'admin'),
            'sortOrder' => 300,
        ));

        // Check for Admin Menu Pages to insert
    }

    public static function onTopMenuInit($event)
    {
        foreach (CustomPage::model()->findAllByAttributes(array('navigation_class' => CustomPage::NAV_CLASS_TOPNAV)) as $page) {
            $event->sender->addItem(array(
                'label' => $page->title,
                'url' => Yii::app()->createUrl('//custom_pages/view', array('id' => $page->id)),
                'target' => ($page->type == CustomPage::TYPE_LINK) ? '_blank' : '',
                'icon' => '<i class="fa ' . $page->icon . '"></i>',
                'isActive' => (Yii::app()->controller->module && Yii::app()->controller->module->id == 'custom_pages' && Yii::app()->controller->id == 'view' && Yii::app()->request->getParam('id') == $page->id),
                'sortOrder' => ($page->sort_order != '') ? $page->sort_order : 1000,
            ));
        }
    }

}
