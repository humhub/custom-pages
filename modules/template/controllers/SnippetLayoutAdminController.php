<?php

namespace humhub\modules\custom_pages\modules\template\controllers;

use Yii;
use humhub\modules\custom_pages\modules\template\models\Template;

/**
 * Controller for managing snippet layout instaces.
 *
 * @author buddha
 */
class SnippetLayoutAdminController extends AdminController
{
    /**
     * @inheritdoc
     */
    public $type = Template::TYPE_SNIPPET_LAYOUT;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->indexHelp = Yii::t(
            'CustomPagesModule.template',
            'Here you can manage your snippet layouts. Snippet layouts are templates, which can be included into sidebars.',
        );
        parent::init();
    }

}
