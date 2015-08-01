<?php

namespace humhub\modules\custom_pages;

use yii\helpers\Url;
use humhub\modules\custom_pages\models\CustomPage;

class Module extends \humhub\components\Module
{

    /**
     * @inheritdoc
     */
    public function getConfigUrl()
    {
        return Url::to(['/custom_pages/admin']);
    }

    /**
     * @inheritdoc
     */
    public function disable()
    {
        foreach (CustomPage::find()->all() as $entry) {
            $entry->delete();
        }

        parent::disable();
    }

}
