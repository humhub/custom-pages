<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\models;

interface TemplateContentIterable
{
    /**
     * Get items of the iterable template content
     *
     * @return iterable
     */
    public function getItems(): iterable;
}
