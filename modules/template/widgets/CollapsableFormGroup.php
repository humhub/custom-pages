<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\widgets;

use Yii;

/**
 * CollapsableFormGroup
 *
 * @author buddha
 */
class CollapsableFormGroup extends \humhub\components\Widget
{
    public $defaultState;
    public $openText;
    public $closeText;
    public $label;

    public function init()
    {
        parent::init();
        ob_start();
    }

    public function run()
    {
        if ($this->openText == null) {
            $this->openText = ($this->label) ? $this->label : Yii::t('CustomPagesModule.template', 'Show more');
        }

        if ($this->closeText == null) {
            $this->closeText = ($this->label) ? $this->label : Yii::t('CustomPagesModule.template', 'Show less');
        }

        return $this->render('collapsableFormGroup', [
            'content' => ob_get_clean(),
            'defaultState' => $this->defaultState,
            'openText' => $this->openText,
            'closeText' => $this->closeText,
        ]);
    }

}
