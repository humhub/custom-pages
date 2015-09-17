<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\models;

use yii\base\Model;

/**
 * AddPageForm selects a page type
 *
 * @author luke
 */
class AddPageForm extends Model
{

    public $type;
    public $availableTypes;

    public function rules()
    {
        return array(
            ['type', 'in', 'range' => array_keys($this->availableTypes)],
        );
    }

}
