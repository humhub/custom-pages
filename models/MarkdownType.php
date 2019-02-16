<?php
/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 13.02.2019
 * Time: 13:28
 */

namespace humhub\modules\custom_pages\models;


use Yii;
use humhub\modules\file\models\File;

class MarkdownType extends ContentType
{
    const ID = 4;

    function getId()
    {
        return static::ID;
    }


    /**
     * @param CustomContentContainer $page
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($page, $insert, $changedAttributes) {
        // TODO: test non deprecation
        //$page->content->fileManager->attach( Yii::$app->request->post('fileUploaderHiddenGuidField'));
        File::attachPrecreated($page, Yii::$app->request->post('fileUploaderHiddenGuidField'));
}

    function getLabel()
    {
        return Yii::t('CustomPagesModule.base', 'MarkDown');
    }

    function getDescription()
    {
       return Yii::t('CustomPagesModule.base', 'Allows you to add content in MarkDown syntax.');
    }
}