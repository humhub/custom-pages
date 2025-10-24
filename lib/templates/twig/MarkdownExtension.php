<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\lib\templates\twig;

use humhub\modules\content\widgets\richtext\RichText;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MarkdownExtension extends AbstractExtension
{
    /**
     * @inheritdoc
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('markdown_strip', [$this, 'strip']),
            new TwigFilter('markdown_html', [$this, 'html']),
            new TwigFilter('markdown_plain', [$this, 'plain']),
            new TwigFilter('markdown_short', [$this, 'short']),
        ];
    }

    public function strip($text): string
    {
        // Remove images ![alt](url)
        $text = preg_replace('/!\[.*?\]\(.*?\)/', '', (string) $text);

        // Remove links [text](url)
        $text = preg_replace('/\[(.*?)\]\(.*?\)/', '$1', $text);

        // Remove headers #, ## и т.д.
        $text = preg_replace('/^#{1,6}\s*/m', '', $text);

        // Remove text formats **bold**, *italic* and etc.
        $text = preg_replace('/(\*\*|__|~~)(.*?)\1/', '$2', $text);
        $text = preg_replace('/(\*|_|~)(.*?)\1/', '$2', $text);
        $text = preg_replace('/`{1,3}(.*?)`{1,3}/', '$1', $text);

        // Remove code blocks
        $text = preg_replace('/```[\s\S]*?```/', '', $text);

        // Remove html tags to be sure
        $text = strip_tags($text);

        return trim($text);
    }

    public function html($text): string
    {
        return RichText::convert($text, RichText::FORMAT_HTML);
    }

    public function plain($text): string
    {
        return RichText::convert($text, RichText::FORMAT_PLAINTEXT);
    }

    public function short($text): string
    {
        return RichText::convert($text, RichText::FORMAT_SHORTTEXT);
    }
}
