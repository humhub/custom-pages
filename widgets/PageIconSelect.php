<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\widgets;

use humhub\components\Widget;

/**
 * PageIconSelect widget renders a icon selection for a given $page
 */
class PageIconSelect extends Widget
{

    public $page;
    
    public function run()
    {
        return $this->render('pageIconSelect', [
            'page' => $this->page,
        ]);
    }
}
