<?php

/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 13.02.2019
 * Time: 13:28
 */

namespace humhub\modules\custom_pages\models;

use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\content\widgets\richtext\RichTextField;
use yii\widgets\ActiveForm;
use Yii;
use humhub\modules\file\models\File;

class MarkdownType extends ContentType
{
    public const ID = 4;

    public function getId()
    {
        return static::ID;
    }


    /**
     * @param CustomContentContainer $page
     * @param bool $insert
     * @param array $changedAttributes
     * @return bool
     * @throws \Exception
     */
    public function afterSave($page, $insert, $changedAttributes)
    {
        if (!parent::afterSave($page, $insert, $changedAttributes)) {
            return false;
        }

        RichText::postProcess($page->page_content, $page);
        return true;
    }

    public function getLabel()
    {
        return Yii::t('CustomPagesModule.base', 'MarkDown');
    }

    public function getDescription()
    {
        return Yii::t('CustomPagesModule.base', 'Allows you to add content in MarkDown syntax.');
    }

    public function render(CustomContentContainer $content, $options = [])
    {
        return RichText::output($content->page_content);
    }

    public function getViewName()
    {
        return 'markdown';
    }

    public function renderFormField(ActiveForm $form, CustomContentContainer $page)
    {
        return  $form->field($page, $page->getPageContentProperty())->widget(RichTextField::class, [ 'pluginOptions' => ['maxHeight' => '500px']]);
    }
}
