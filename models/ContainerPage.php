<?php

namespace humhub\modules\custom_pages\models;

use Yii;

/**
 * This is the model class for table "custom_pages_container_page".
 *
 * The followings are the available columns in table 'custom_pages_container_page':
 * @property integer $id
 * @property integer $type
 * @property string $title
 * @property string $icon
 * @property string $page_content
 * @property integer $in_new_window
 * @property integer $sort_order
 */
class ContainerPage extends \humhub\modules\content\components\ContentActiveRecord implements \humhub\modules\search\interfaces\Searchable
{

    public $autoAddToWall = false;
    public $url;

    const TYPE_LINK = '1';
    const TYPE_HTML = '2';
    const TYPE_IFRAME = '3';
    const TYPE_MARKDOWN = '4';

    /**
     * @inheritdoc
     */
    public $wallEntryClass = 'humhub\modules\custom_pages\widgets\WallEntry';

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'custom_pages_container_page';
    }

    public function rules()
    {
        return array(
            [['type', 'title'], 'required'],
            [['type', 'sort_order', 'in_new_window'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['icon'], 'string', 'max' => 100],
            [['page_content', 'url'], 'safe'],
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
            'page_content' => Yii::t('CustomPagesModule.base', 'Content'),
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
            $this->page_content = $this->url;
        }
        return parent::beforeSave($insert);
    }

    public function afterFind()
    {
        if ($this->type == self::TYPE_IFRAME || $this->type == self::TYPE_LINK) {
            $this->url = $this->page_content;
        }

        return parent::afterFind();
    }

    public static function getPageTypes()
    {
        return array(
            self::TYPE_MARKDOWN => Yii::t('CustomPagesModule.base', 'MarkDown'),
            self::TYPE_LINK => Yii::t('CustomPagesModule.base', 'Link'),
            self::TYPE_IFRAME => Yii::t('CustomPagesModule.base', 'IFrame'),
        );
    }

    /**
     * @inheritdoc
     */
    public function getContentName()
    {
        return 'Page';
    }

    /**
     * @inheritdoc
     */
    public function getContentDescription()
    {
        return $this->title;
    }

    /**
     * @inheritdoc
     */
    public function getSearchAttributes()
    {
        return array(
            'title' => $this->title,
            'content' => $this->page_content,
        );
    }

    /**
     * @inheritdoc
     */
    public function getUrl()
    {
        return $this->content->container->createUrl('/custom_pages/container/view', ['id' => $this->id]);
    }

}
