<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\widgets;

use humhub\modules\custom_pages\models\ContentType;
use humhub\modules\custom_pages\models\Target;

/**
 * Content Type Row for adding a new Page/Snippet of a certain type
 *
 * @author buddha
 */
class AddContentTypeRow extends \yii\base\Widget
{

    /**
     * @var ContentType
     */
    public $contentType;
    public $disabled = false;

    /**
     * @var Target
     */
    public $target;

    /**
     * @var string
     */
    public $pageType;
    
    public $hide = false;
    
    public function run()
    {
        if(!$this->hide) {
            return $this->render('addContentTypeRow', [
                'target' => $this->target,
                'pageType' => $this->pageType,
                'contentType' => $this->contentType,
                'disabled' => $this->disabled
            ]);
        }
    }
}
