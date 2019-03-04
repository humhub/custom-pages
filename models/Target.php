<?php


namespace humhub\modules\custom_pages\models;


use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\custom_pages\Module;
use yii\base\Model;

/**
 * Class Target defines a target of a page
 * @package humhub\modules\custom_pages\models
 */
class Target extends Model
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $contentName;

    /**
     * @var
     */
    public $icon = Module::ICON;

    /**
     * @var ContentContainerActiveRecord
     */
    public $container;

    /**
     * @var string
     */
    public $editBackUrl;

    /**
     * @var array defines allowed content types for this target, if empty all types are allowed
     */
    public $contentTypes = [];

    /**
     * @var string used to create the access url
     */
    public $accessRoute = '/custom_pages/view';

    /**
     * @var string defines a sublayout used when rendering an entry
     */
    public $subLayout;

    /**
     * [
     * 'sortOrder' => ['value' => 0, 'hidden' => true],
     * 'icon' => ['value' => 'adjust', 'readonly' => true],
     * 'title' => ['value']
     * ]
     * @var array
     */
    public $fieldSettings = [];

    /**
     * Returns the url for accessing this content. Targets can change the access url by overwriting the default
     * [[accessRoute]].
     * @param CustomContentContainer $content
     * @return string
     */
    public function getContentUrl(CustomContentContainer $content)
    {
        return $content->content->container
            ? $content->content->container->createUrl($this->accessRoute, ['id' => $content->id])
            : Url::toRoute([$this->accessRoute, 'id' => $content->id]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name'], 'required']
        ];
    }

    public function getIcon()
    {
        return $this->icon;
    }

    public function isAllowedContentType($type)
    {
        if($type instanceof ContentType) {
            $type = $type->getId();
        }
        return empty($this->contentTypes) || in_array($type, $this->contentTypes);
    }

    /**
     * @param $field string
     * @param $setting array|bool
     */
    public function setFieldSetting($field, $setting)
    {
        $this->fieldSettings[$field] = $setting;
    }

    /**
     * @param $field string
     * @param $setting array|bool
     * @return bool
     */
    public function isAllowedField($field)
    {
        if(!isset($this->fieldSettings[$field])) {
            return true;
        }

        if($this->fieldSettings[$field] === false) {
            return false;
        }

        return true;
    }

    public function getSubLayout()
    {
        return $this->subLayout;
    }

}