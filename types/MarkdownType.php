<?php
/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 13.02.2019
 * Time: 13:28
 */

namespace humhub\modules\custom_pages\types;

use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\content\widgets\richtext\RichTextField;
use humhub\modules\custom_pages\models\CustomPage;
use Yii;
use yii\widgets\ActiveForm;

class MarkdownType extends ContentType
{
    public const ID = 4;

    public function getLabel(): string
    {
        return Yii::t('CustomPagesModule.base', 'MarkDown');
    }

    public function getDescription(): string
    {
        return Yii::t('CustomPagesModule.base', 'Allows you to add content in MarkDown syntax.');
    }

    public function render(CustomPage $content, $options = []): string
    {
        return RichText::output($content->page_content);
    }

    public function getViewName(): string
    {
        return 'markdown';
    }

    public function renderFormField(ActiveForm $form, CustomPage $page): string
    {
        return $form->field($page, 'page_content')->widget(RichTextField::class, [
            'pluginOptions' => ['maxHeight' => '500px'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function afterSave(CustomPage $page, bool $insert, array $changedAttributes): bool
    {
        if (!parent::afterSave($page, $insert, $changedAttributes)) {
            return false;
        }

        RichText::postProcess($page->page_content, $page);
        return true;
    }
}
