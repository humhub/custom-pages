<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\components\ActiveRecord;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use humhub\widgets\JsWidget;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "custom_pages_template_element_container_item".
 *
 * @property int $id
 * @property int $element_content_id
 * @property int $sort_order
 * @property string $title
 *
 * @property-read ContainerElement $container
 * @property-read TemplateInstance $templateInstance
 * @property-read Template $template
 * @property-read CustomPage $page
 */
class ContainerItem extends ActiveRecord
{
    public ?int $pageId = null;
    public ?int $templateId = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'custom_pages_template_element_container_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pageId', 'templateId', 'element_content_id'], 'required'],
            [['pageId', 'templateId', 'element_content_id', 'sort_order'], 'integer'],
            ['title', 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        $templateInstance = $this->templateInstance;
        if ($templateInstance) {
            $this->pageId = $templateInstance->page_id;
            $this->templateId = $templateInstance->template_id;
        }

        parent::afterFind();
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $templateInstance = new TemplateInstance();
            $templateInstance->page_id = $this->pageId;
            $templateInstance->template_id = $this->templateId;
            $templateInstance->container_item_id = $this->id;
            $templateInstance->save();
        }

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        TemplateInstance::findOne(['container_item_id' => $this->id])?->delete();
        return parent::beforeDelete();
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        parent::afterDelete();

        // Refresh sort orders of the rest items after deletion
        $items = self::find()
            ->where(['element_content_id' => $this->element_content_id])
            ->orderBy(['sort_order' => SORT_ASC]);
        $sortOrder = 0;
        foreach ($items->each() as $item) {
            $item->sort_order = $sortOrder++;
            $item->update();
        }
    }

    public static function incrementIndex($containerId, $index)
    {
        self::updateAllCounters(['sort_order' => 1], ['and', ['>=', 'sort_order', $index], ['element_content_id' => $containerId]]);
    }

    public static function incrementBetween($containerId, $start, $end)
    {
        self::updateAllCounters(['sort_order' => 1], ['and', ['>=', 'sort_order', $start], ['<=', 'sort_order', $end], ['element_content_id' => $containerId]]);
    }

    public static function decrementIndex($containerId, $index)
    {
        self::updateAllCounters(['sort_order' => -1], ['and', ['<=', 'sort_order', $index], ['element_content_id' => $containerId]]);
    }

    public static function decrementBetween($containerId, $start, $end)
    {
        self::updateAllCounters(['sort_order' => -1], ['and', ['>=', 'sort_order', $start], ['<=', 'sort_order', $end], ['element_content_id' => $containerId]]);
    }

    public function getTemplateInstance(): ActiveQuery
    {
        return $this->hasOne(TemplateInstance::class, ['container_item_id' => 'id']);
    }

    public function getTemplate(): ActiveQuery
    {
        return $this->hasOne(Template::class, ['id' => 'template_id'])
            ->via('templateInstance');
    }

    public function getPage(): ActiveQuery
    {
        return $this->hasOne(CustomPage::class, ['id' => 'page_id'])
            ->via('templateInstance');
    }

    public function getContainer(): ActiveQuery
    {
        return $this->hasOne(ContainerElement::class, ['id' => 'element_content_id']);
    }

    public function render(string $mode, $inline = false): string
    {
        try {
            $result = $this->template->render($this->templateInstance, $mode);
            return $mode === 'edit' ? $this->renderEditBlock($result, $inline) : $result;
        } catch (\Throwable $ex) {
            Yii::error('Broken Container Item #' . $this->id . ' has lost Template Instance. ' .
                'Error: ' . $ex->getMessage() . ' ' . $ex->getFile() . '(' . $ex->getLine() . ') ' . $ex->getTraceAsString(), 'custom-pages');
            return 'Broken Container Item #' . $this->id . ', please delete and create it again.';
        }
    }

    public function renderEditBlock(string $content, $inline): string
    {
        return JsWidget::widget([
            'jsWidget' => 'custom_pages.template.TemplateContainerItem',
            'content' => $content,
            'options' => [
                'class' => $inline ? 'inline' : '',
                'data-template-item' => $this->id,
                'data-template-item-title' => $this->title,
            ],
        ]);
    }
}
