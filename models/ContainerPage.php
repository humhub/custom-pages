<?php

namespace humhub\modules\custom_pages\models;

use Yii;
use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\custom_pages\models\forms\SettingsForm;
use humhub\modules\search\interfaces\Searchable;
use humhub\modules\custom_pages\components\Container;
use humhub\modules\custom_pages\modules\template\models\Template;

/**
 * This is the model class for table "custom_pages_container_page".
 *
 * A container page is space related custom page container.
 *
 */
class ContainerPage extends Page implements Searchable
{

    const NAV_CLASS_SPACE_NAV = 'Space Navigation';

    /**
     * @inheritdoc
     */
    public $streamChannel = null;

    /**
     * @inheritdoc
     */
    public $autoAddToWall = false;

    /**
     * @inheritdoc
     */
    public $wallEntryClass = 'humhub\modules\custom_pages\widgets\WallEntry';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            ['class' => Container::class],
        ];
    }

    /**
     * Returns a navigation selection for all navigations this page can be added.
     * @return array
     */
    public static function getNavigationClasses()
    {
        return [
            self::NAV_CLASS_SPACE_NAV => Yii::t('CustomPagesModule.base', 'Space Navigation')
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
        $rules[] = [['page_content'], 'safe'];
        return $rules;
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        $result = $this->defaultAttributeLabels();
        $result['in_new_window'] = Yii::t('CustomPagesModule.models_ContainerPage', 'Open in new window');

        if(PhpType::isType($this->getContentType())) {
            $contentLabel = Yii::t('CustomPagesModule.models_Page', 'View');
        } else {
            $contentLabel = Yii::t('CustomPagesModule.components_Container', 'Content');
        }

        $result['page_content'] = $contentLabel;
        $result['admin_only'] = Yii::t('CustomPagesModule.models_ContainerPage', 'Only visible for space admins');
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getSearchAttributes()
    {
        return [
            'title' => $this->title,
            'content' => $this->page_content,
        ];
    }

    /**
     * Returns the view url of this page.
     */
    public function getUrl()
    {
        return $this->content->container->createUrl('/custom_pages/container/view', ['id' => $this->id]);
    }

    /**
     * @inheritdoc
     */
    public function getContentTypes()
    {
        return [
            Container::TYPE_MARKDOWN,
            Container::TYPE_LINK,
            Container::TYPE_IFRAME,
            Container::TYPE_TEMPLATE,
            Container::TYPE_PHP
        ];
    }

    /**
     * @inheritdoc
     */
    public function getAllowedTemplateSelection()
    {
        return Template::getSelection(['type' => Template::TYPE_LAYOUT, 'allow_for_spaces' => 1]);
    }

    /**
     * @inheritdoc
     */
    public function getPhpViewPath()
    {
        $settings = new SettingsForm();
        return $settings->phpContainerPagePath;
    }

    /**
     * @return string
     */
    public function getEditUrl()
    {
        return Url::toEditPage($this->id, $this->content->container);
    }
}
