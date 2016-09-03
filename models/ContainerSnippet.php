<?php

namespace humhub\modules\custom_pages\models;

use Yii;
use humhub\modules\custom_pages\components\Container;
use humhub\modules\custom_pages\modules\template\models\Template;
/**
 * This is the model class for table "custom_pages_container_snipped".
 *
 * The followings are the available columns in table 'custom_pages_container_page':
 * @property integer $id
 * @property integer $type
 * @property string $title
 * @property string $icon
 * @property string $page_content
 * @property integer $sort_order
 */
class ContainerSnippet extends ContainerPage
{

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'custom_pages_container_snippet';
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function rules()
    {
        $rules = $this->defaultRules();
        $rules[] = ['page_content', 'safe'];
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function getContentName()
    {
        return 'Snippet';
    }

    /**
     * @inheritdoc
     */
    public function getUrl()
    {
        return $this->content->container->createUrl('/custom_pages/container-snippet/view', ['id' => $this->id]);
    }

    public function getContentTypes()
    {
        return [
            Container::TYPE_MARKDOWN,
            Container::TYPE_IFRAME,
            Container::TYPE_TEMPLATE,
        ];
    }

    public function getLabel()
    {
        return Yii::t('CustomPagesModule.models_ContainerSnippet', 'snippet');
    }
    
     public function getAllowedTemplateSelection()
    {
        return Template::getSelection(['type' => Template::TYPE_SNIPPED_LAYOUT]);
    }
}
