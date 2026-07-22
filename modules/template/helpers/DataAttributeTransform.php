<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\helpers;

use HTMLPurifier_AttrTransform;

/**
 * HTMLPurifier by default has no notion of `data-*` attributes, so they get
 * stripped by the attribute whitelist like any other unknown attribute.
 *
 * This transform is registered both as a pre- and as a post- attribute
 * transform (see {@see BaseElementContent::purify()}). On the pre-pass (run
 * before whitelist validation) it stashes away any `data-*` attribute in the
 * current context. On the post-pass (run after whitelist validation, once
 * the stash already exists) it merges the stashed attributes back in, since
 * by then they would otherwise have already been removed for not being on
 * the whitelist.
 */
class DataAttributeTransform extends HTMLPurifier_AttrTransform
{
    private const CONTEXT_KEY = 'CustomPagesDataAttributeStash';

    /**
     * @inheritdoc
     */
    public function transform($attr, $config, $context)
    {
        if ($context->exists(self::CONTEXT_KEY)) {
            // Post-pass: restore the data-* attributes stashed in the pre-pass
            $stash = $context->get(self::CONTEXT_KEY);
            foreach ($stash as $name => $value) {
                $attr[$name] = $value;
            }
            $context->destroy(self::CONTEXT_KEY);

            return $attr;
        }

        // Pre-pass: stash away data-* attributes so they survive whitelist validation.
        // Values containing `<` or `>` are dropped: HTMLPurifier's output escaping only
        // protects the HTML document itself, not what a browser hands back to JS reading
        // the attribute (e.g. via `.dataset`/`.data()`), which is HTML-entity-decoded
        // again. Disallowing angle brackets in the value prevents it from ever looking
        // like markup again, even if some script naively inserts it via `.html()`.
        $stash = [];
        foreach ($attr as $name => $value) {
            if (is_string($name)
                && is_string($value)
                && preg_match('/^data-[a-z0-9-]+$/i', $name)
                && strpbrk($value, '<>') === false
            ) {
                $stash[$name] = $value;
            }
        }
        $context->register(self::CONTEXT_KEY, $stash);

        return $attr;
    }
}
