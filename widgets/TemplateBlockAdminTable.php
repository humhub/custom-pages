<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\widgets;

/**
 * User Administration Menu
 *
 * @author Basti
 */
class TemplateBlockAdminTable extends \humhub\components\Widget
{
    public $template;

    public function run()
    {
        return $this->render('templateBlockAdminTable', [
            'template' => $this->template
        ]);
    }

}
