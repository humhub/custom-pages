<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\helpers;

use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\rest\definitions\ContentDefinitions;
use Throwable;
use Yii;
use yii\helpers\Url;

class RestDefinitions
{
    public static function getCustomPage(CustomPage $page): array
    {
        $renderedContent = static::getRenderedContent($page);

        return [
            'id' => $page->id,
            'title' => $page->title,
            'type' => $page->type,
            'target' => $page->target,
            'icon' => $page->icon,
            'sort_order' => $page->sort_order,
            'in_new_window' => (bool)$page->in_new_window,
            'url' => $page->url,
            'abstract' => $page->abstract,
            'cssClass' => $page->cssClass,
            'page_content' => $page->page_content,
            'rendered_content' => $renderedContent,
            'text_content' => static::getTextContent($renderedContent),
            'visibility' => $page->visibility,
            'hide_menu' => (bool)$page->hide_menu,
            'page_type' => $page->getPageType(),
            'template_id' => $page->getTemplateId(),
            'page_url' => $page->getUrl(),
            'permalink' => static::getPagePermalink($page),
            'content' => ContentDefinitions::getContent($page->content),
        ];
    }

    public static function getPagePermalink(CustomPage $page): string
    {
        return Url::to(['/content/perma', 'id' => $page->content->id], true);
    }

    private static function getRenderedContent(CustomPage $page): ?string
    {
        try {
            return $page->render();
        } catch (Throwable $e) {
            Yii::error($e);
            return null;
        }
    }

    private static function getTextContent(?string $renderedContent): ?string
    {
        if ($renderedContent === null) {
            return null;
        }

        $text = html_entity_decode(strip_tags($renderedContent), ENT_QUOTES | ENT_HTML5, Yii::$app->charset);
        $normalizedText = preg_replace('/[\r\n\s]+/u', ' ', $text);

        return trim($normalizedText ?? $text);
    }
}