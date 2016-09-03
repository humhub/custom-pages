<?php

namespace humhub\modules\custom_pages\models;

use Yii;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\search\interfaces\Searchable;
use humhub\modules\custom_pages\components\Container;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\models\CustomContentContainer;
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
class ContainerPage extends ContentActiveRecord implements Searchable, CustomContentContainer
{

    public $autoAddToWall = false;

    /**
     * @inheritdoc
     */
    public $wallEntryClass = 'humhub\modules\custom_pages\widgets\WallEntry';

    public function behaviors()
    {
        return [
            ['class' => \humhub\modules\custom_pages\components\Container::className()],
        ];
    }

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'custom_pages_container_page';
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function rules()
    {
        $rules = $this->defaultRules();
        $rules[] = ['in_new_window', 'integer'];
        $rules[] = ['page_content', 'safe'];
        return $rules;
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        $result = $this->defaultAttributeLabels();
        $result['in_new_window'] = Yii::t('CustomPagesModule.models_ContainerPage', 'Open in new window');
        $result['page_content'] = Yii::t('CustomPagesModule.models_ContainerPage', 'Content');
        return $result;
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

    public function getContentTypes()
    {
        return [
            Container::TYPE_MARKDOWN,
            Container::TYPE_LINK,
            Container::TYPE_IFRAME,
            Container::TYPE_TEMPLATE,
        ];
    }

    public function getLabel()
    {
        return Yii::t('CustomPagesModule.models_ContainerPage', 'page');
    }
    
     public function getPageContent()
    {
        return $this->page_content;
    }

    public function getAllowedTemplateSelection()
    {
        return Template::getSelection(['type' => Template::TYPE_LAYOUT, 'allow_for_spaces' => 1]);
    }

}
