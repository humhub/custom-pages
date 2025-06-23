<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\types;

use humhub\modules\custom_pages\models\CustomPage;
use yii\base\StaticInstanceTrait;
use yii\widgets\ActiveForm;

abstract class ContentType
{
    use StaticInstanceTrait;

    protected ?CustomPage $customPage = null;

    /**
     * @var bool $hasContent Set false if the Type has no a content
     */
    protected bool $hasContent = true;

    abstract public function getLabel(): string;

    abstract public function getDescription(): string;

    abstract public function getViewName(): string;

    abstract public function render(CustomPage $content, $options = []): string;

    abstract public function renderFormField(ActiveForm $form, CustomPage $page): string;

    /**
     * Get all available types
     *
     * @return ContentType[]
     */
    final public static function getContentTypes(): array
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

    public function getId(): int
    {
        return static::ID;
    }

    /**
     * Get Content Type by ID
     *
     * @param int|string|null $type
     * @return ContentType|null
     * @deprecated since 1.11.1
     */
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

    /**
     * Get Content Type by Custom Page
     *
     * @param CustomPage $page
     * @return ContentType|null
     */
    public static function getByPage(CustomPage $page): ?ContentType
    {
        // Note: Don't use here ContentType::instance() because it is cached only per class name,
        //       to avoid errors on duplicating of a Custom Page.
        $type = match($page->type) {
            MarkdownType::ID => new MarkdownType(),
            LinkType::ID => new LinkType(),
            IframeType::ID => new IframeType(),
            TemplateType::ID => new TemplateType(),
            HtmlType::ID => new HtmlType(),
            PhpType::ID => new PhpType(),
            default => null,
        };

        return $type?->setCustomPage($page);
    }

    /**
     * Check the Type has a content
     *
     * @return bool
     */
    public function hasContent(): bool
    {
        return $this->hasContent;
    }

    /**
     * Check the requested ID or Content Type is the current Content Type
     *
     * @param int|ContentType $id
     * @return bool
     */
    public static function isType($id): bool
    {
        if ($id instanceof self) {
            $id = $id->getId();
        }

        return static::instance()->getId() == $id;
    }

    /**
     * Run it after the Custom Page has been saved
     *
     * @param CustomPage $page
     * @param bool $insert
     * @param array $changedAttributes
     * @return bool
     */
    public function afterSave(CustomPage $page, bool $insert, array $changedAttributes): bool
    {
        return true;
    }

    /**
     * Run it after the Custom Page has been deleted
     *
     * @param CustomPage $page
     * @return void
     */
    public function afterDelete(CustomPage $page): void
    {
    }

    /**
     * Set Custom Page
     *
     * @param CustomPage $page
     * @return self
     */
    public function setCustomPage(CustomPage $page): self
    {
        $this->customPage = $page;
        return $this;
    }

    /**
     * Set properties to new duplicating custom page
     *
     * @param CustomPage $newPage
     * @return bool
     */
    public function beforeDuplicate(CustomPage $newPage): bool
    {
        $newPage->visibility = $this->customPage->visibility;

        foreach ($this->customPage->attributes as $attrKey => $attrValue) {
            if ($attrKey !== 'id') {
                $newPage->$attrKey = $attrValue;
            }
        }

        foreach ($this->customPage->content->attributes as $attrKey => $attrValue) {
            if (!in_array($attrKey, ['id', 'guid', 'object_model', 'object_id', 'created_at', 'created_by', 'updated_at', 'updated_by'])) {
                $newPage->content->$attrKey = $attrValue;
            }
        }

        return true;
    }

    /**
     * Duplicate Custom Page
     *
     * @param array|null $loadData
     * @return CustomPage
     */
    public function duplicate(?array $loadData = null): CustomPage
    {
        $newPage = new CustomPage([
            'type' => $this->getId(),
            'target' => $this->customPage->target,
        ]);

        if (!$this->beforeDuplicate($newPage)) {
            return $newPage;
        }

        if (is_array($loadData) && !$newPage->load($loadData)) {
            return $newPage;
        }

        if (!$newPage->validate() || !$newPage->save()) {
            return $newPage;
        }

        if (!empty($newPage->url) && $newPage->url === $this->customPage->url) {
            // Make URL unique
            $newPage->updateAttributes(['url' => $newPage->url . '-' . $newPage->id]);
        }

        return $newPage;
    }
}
