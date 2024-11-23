<?php

namespace humhub\modules\custom_pages\models;

use Yii;
use yii\base\Model;
use yii\widgets\ActiveForm;

abstract class ContentType extends Model
{
    protected $hasContent = true;

    abstract public function getId();

    public static function getById($type): ?ContentType
    {
        switch ((int) $type) {
            case MarkdownType::ID:
                return MarkdownType::instance();
            case LinkType::ID:
                return LinkType::instance();
            case IframeType::ID:
                return IframeType::instance();
            case TemplateType::ID:
                return TemplateType::instance();
            case HtmlType::ID:
                return HtmlType::instance();
            case PhpType::ID:
                return PhpType::instance();
            default:
                return null;
        }
    }

    abstract public function getLabel();

    abstract public function getViewName();

    abstract public function render(CustomPage $content, $options = []);

    abstract public function getDescription();

    abstract public function renderFormField(ActiveForm $form, CustomPage $page);

    public function getContentLabel()
    {
        return Yii::t('CustomPagesModule.base', 'Content');
    }

    /**
     * @return bool
     */
    public function hasContent()
    {
        return $this->hasContent;
    }

    public function is($type)
    {
        return $this->getId() === $type;
    }

    /**
     * @param CustomPage $page
     * @param bool $insert
     * @param array $changedAttributes
     * @return bool
     */
    public function afterSave($page, $insert, $changedAttributes)
    {
        return true;
    }

    public function afterDelete($page)
    {
    }

    /**
     * @param $id int|ContentType
     * @return bool
     */
    public static function isType($id)
    {
        if ($id instanceof self) {
            $id = $id->getId();
        }

        return static::instance()->getId() == $id;
    }

    /**
     * @return ContentType[]
     */
    final public static function getContentTypes()
    {
        return [
            MarkdownType::instance(),
            LinkType::instance(),
            IframeType::instance(),
            TemplateType::instance(),
            HtmlType::instance(),
            PhpType::instance(),
        ];
    }
}
