<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\models\filters;

use humhub\modules\content\models\Content;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\stream\models\filters\StreamQueryFilter;

class CustomPageStreamFilter extends StreamQueryFilter
{
    public function apply()
    {
        $excludeContentIds = [];
        foreach ($this->query->all() as $content) {
            /* @var Content $content */
            if ($content->object_model === CustomPage::class) {
                $customPage = $content->getModel();
                if ($customPage instanceof CustomPage && !$customPage->canView()) {
                    $excludeContentIds[] = $content->id;
                }
            }
        }

        if ($excludeContentIds !== []) {
            $this->query->andWhere(['NOT IN', Content::tableName() . '.id', $excludeContentIds]);
        }
    }
}
