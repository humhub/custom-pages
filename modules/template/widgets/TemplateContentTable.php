<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\widgets;

use humhub\components\Widget;

/**
 * User Administration Menu
 *
 * @author Basti
 */
class TemplateContentTable extends Widget
{
    public $template;

    public function run()
    {
        return $this->render('templateElementAdminTable', [
            'elements' => $this->template->elements,
        ]);
    }

}
