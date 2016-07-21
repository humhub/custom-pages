<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\models\forms;

use Yii;
use humhub\modules\custom_pages\models\TemplateBlock;

/**
 * Description of UserGroupForm
 *
 * @author buddha
 */
class AddTemplateBlockForm extends TemplateBlockForm
{
    public function rules()
    {
        return [
            [['templateId', 'type'], 'required']
        ];
    }
    
}
