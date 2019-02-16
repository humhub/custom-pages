<?php


namespace humhub\modules\custom_pages\models;

use yii\base\Model;

abstract class ContentType extends Model
{

    protected $hasContent = true;

    protected $isUrlContent = false;

    abstract function getId();

    public static function getById($type)
    {
        $type = (int) $type;

        switch ($type) {
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
        }
    }

    public abstract function getLabel();

    public abstract function getDescription();

    /**
     * @return bool
     */
    public function hasContent() {
        return $this->hasContent;
    }

    /**
     * @return bool
     */
    public function isUrlContent() {
        return $this->isUrlContent;
    }

    /**
     * @param CustomContentContainer $page
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($page, $insert, $changedAttributes) {}

    public static function isType($id)
    {
        if($id instanceof ContentType) {
            $id = $id->getId();
        }

        return static::instance()->getId() == $id;
    }

    /**
     * @return ContentType[]
     */
    public final static function getContentTypes()
    {
        return [
            MarkdownType::instance(),
            LinkType::instance(),
            IframeType::instance(),
            TemplateType::instance(),
            HtmlType::instance(),
            PhpType::instance()
        ];
    }
}