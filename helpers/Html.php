<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\helpers;

use humhub\libs\Html as BaseHtml;

class Html extends BaseHtml
{
    /**
     * Add attribute "nonce" for all script tags found in the given content
     *
     * @param string|null $content
     * @return string
     */
    public static function applyScriptNonce(?string $content): string
    {
        return $content === null
            ? ''
            : preg_replace_callback('/(<script)(.*?>)/i', [self::class, 'applyScriptNonceCallback'], $content);
    }

    protected static function applyScriptNonceCallback(array $m): string
    {
        $attrs = str_replace([' nonce=""', " nonce=''", ' nonce'], '', $m[2]);
        return $m[1] . ' ' . self::nonce() . $attrs;
    }
}
