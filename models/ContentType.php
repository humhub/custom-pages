<?php


namespace humhub\modules\custom_pages\models;

use Yii;
use yii\base\Model;
use yii\widgets\ActiveForm;

abstract class ContentType extends Model
{

    protected $hasContent = true;

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

    public abstract function getViewName();

    public abstract function render(CustomContentContainer $content, $options = []);

    public abstract function getDescription();

    public abstract function renderFormField(ActiveForm $form, CustomContentContainer $page);

    public function getContentLabel() {
        return Yii::t('CustomPagesModule.components_Container', 'Content');
    }

    /**
     * @return bool
     */
    public function hasContent() {
        return $this->hasContent;
    }

    public function is($type)
    {
        return $this->getId() === $type;
    }

    /**
     * @param CustomContentContainer $page
     * @param bool $insert
     * @param array $changedAttributes
     * @return bool
     */
    public function afterSave($page, $insert, $changedAttributes) {
        return true;
    }

    public function afterDelete($page) {}

    /**
     * @param $id int|ContentType
     * @return bool
     */
    public static function isType($id)
    {
        if($id instanceof self) {
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