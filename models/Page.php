<?php

namespace humhub\modules\custom_pages\models;

use Yii;
use humhub\components\ActiveRecord;

/**
 * This is the model class for table "custom_pages_page".
 *
 * The followings are the available columns in table 'custom_pages_page':
 * @property integer $id
 * @property integer $type
 * @property string $title
 * @property string $icon
 * @property string $content
 * @property integer $sort_order
 * @property integer $admin_only
 * @property integer $in_new_window
 * @property string $navigation_class
 */
class Page extends ActiveRecord
{

    public $url;

    const NAV_CLASS_TOPNAV = 'TopMenuWidget';
    const NAV_CLASS_ACCOUNTNAV = 'AccountMenuWidget';
    const NAV_CLASS_EMPTY = 'WithOutMenu';
    const TYPE_LINK = '1';
    const TYPE_HTML = '2';
    const TYPE_IFRAME = '3';
    const TYPE_MARKDOWN = '4';

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'custom_pages_page';
    }

    public function rules()
    {
        return array(
            [['type', 'title', 'navigation_class'], 'required'],
            [['type', 'sort_order', 'admin_only', 'in_new_window'], 'integer'],
            [['title', 'navigation_class'], 'string', 'max' => 255],
            [['icon'], 'string', 'max' => 100],
            [['content', 'url'], 'safe'],
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',         
            'type' => Yii::t('CustomPagesModule.base', 'Type'),
            'title' => Yii::t('CustomPagesModule.base', 'Title'),
            'icon' => Yii::t('CustomPagesModule.base', 'Icon'),
            'content' => Yii::t('CustomPagesModule.base', 'Content'),
            'url' => Yii::t('CustomPagesModule.base', 'URL'),
            'sort_order' => Yii::t('CustomPagesModule.base', 'Sort Order'),
            'admin_only' => Yii::t('CustomPagesModule.base', 'Only visible for admins'),
            'in_new_window' => Yii::t('CustomPagesModule.base', 'Open in new window'),
            'navigation_class' => Yii::t('CustomPagesModule.base', 'Navigation'),
        );
    }

    public function beforeSave($insert)
    {
        if ($this->type == self::TYPE_IFRAME || $this->type == self::TYPE_LINK) {
            $this->content = $this->url;
        }

        return parent::beforeSave($insert);
    }

    public function afterFind()
    {
        if ($this->type == self::TYPE_IFRAME || $this->type == self::TYPE_LINK) {
            $this->url = $this->content;
        }

        return parent::afterFind();
    }

    public static function getNavigationClasses()
    {
        return array(
            self::NAV_CLASS_TOPNAV => Yii::t('CustomPagesModule.base', 'Top Navigation'),
            self::NAV_CLASS_ACCOUNTNAV => Yii::t('CustomPagesModule.base', 'User Account Menu (Settings)'),
            self::NAV_CLASS_EMPTY => Yii::t('CustomPagesModule.base', 'Without adding to navigation (Direct link)'),
        );
    }

    public static function getPageTypes()
    {
        return array(
            self::TYPE_LINK => Yii::t('CustomPagesModule.base', 'Link'),
            self::TYPE_HTML => Yii::t('CustomPagesModule.base', 'HTML'),
            self::TYPE_MARKDOWN => Yii::t('CustomPagesModule.base', 'MarkDown'),
            self::TYPE_IFRAME => Yii::t('CustomPagesModule.base', 'IFrame'),
        );
    }

}
