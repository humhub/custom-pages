<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\services;

use humhub\modules\custom_pages\modules\template\elements\BaseElementContent;
use humhub\modules\custom_pages\modules\template\elements\ContainerElement;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use humhub\modules\custom_pages\services\DuplicatorService;
use humhub\modules\file\models\File;

/**
 * Service to duplicate Element Contents from one Template Instance to another
 */
class TemplateInstanceDuplicatorService
{
    private TemplateInstance $sourceTemplateInstance;

    public function __construct(TemplateInstance $sourceTemplateInstance)
    {
        $this->sourceTemplateInstance = $sourceTemplateInstance;
    }

    /**
     * Duplicate Template Element Contents linked from the source Custom Page
     *
     * @param TemplateInstance $targetTemplateInstance
     * @param int|null $sourceContainerItemId
     * @return void
     * @throws \yii\db\Exception
     */
    public function duplicate(TemplateInstance $targetTemplateInstance, int $sourceContainerItemId = null): void
    {
        $elementContents = BaseElementContent::find()
            ->leftJoin('custom_pages_template_instance', 'template_instance_id = custom_pages_template_instance.id')
            ->where(['page_id' => $this->sourceTemplateInstance->page_id])
            ->andWhere(['container_item_id' => $sourceContainerItemId]);

        foreach ($elementContents->each() as $elementContent) {
            /* @var BaseElementContent $elementContent */
            $copyElementContent = clone $elementContent;
            $copyElementContent->id = null;
            $copyElementContent->setIsNewRecord(true);
            $copyElementContent->template_instance_id = $targetTemplateInstance->id;

            if (!$copyElementContent->save()) {
                continue;
            }

            $this->duplicateAttachments($elementContent, $copyElementContent);

            if ($elementContent instanceof ContainerElement) {
                foreach ($elementContent->items as $item) {
                    $copyItem = clone $item;
                    $copyItem->id = null;
                    $copyItem->setIsNewRecord(true);
                    $copyItem->element_content_id = $copyElementContent->id;
                    if ($copyItem->save()) {
                        $this->duplicate($copyItem->templateInstance, $item->id);
                    }
                }
            }
        }
    }

    /**
     * Duplicate all attached files from source to target Element Content
     *
     * @param BaseElementContent $source
     * @param BaseElementContent $target
     * @return void
     */
    protected function duplicateAttachments(BaseElementContent $source, BaseElementContent $target): void
    {
        foreach (File::findByRecord($source) as $sourceFile) {
            $newFile = DuplicatorService::duplicateFile($sourceFile, $target);
            if ($newFile && is_array($target->dyn_attributes) && $target->dyn_attributes !== []) {
                foreach ($target->dyn_attributes as $attrKey => $attrValue) {
                    $target->$attrKey = str_replace($sourceFile->guid, $newFile->guid, $attrValue);
                }
                $target->save();
            }
        }
    }
}
