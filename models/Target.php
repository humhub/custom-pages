<?php


namespace humhub\modules\custom_pages\models;


use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\custom_pages\helpers\Url;
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
     * @var
     */
    public $icon;

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
     * [
     * 'sortOrder' => ['value' => 0, 'hidden' => true],
     * 'icon' => ['value' => 'adjust', 'readonly' => true],
     * 'title' => ['value']
     * ]
     * @var array
     */
    public $fieldSettings = [];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name'], 'required']
        ];
    }

    public function isAllowedContentType($type)
    {
        return empty($this->contentTypes) || in_array($type, $this->contentTypes);
    }

    public function getEditBackUrl()
    {
        if($this->editBackUrl) {
            return $this->editBackUrl;
        }

        return $this->container ? $this->container->createUrl('/custom_pages/container/list') : Url::to(['/custom_pages/admin/index']);
    }
}