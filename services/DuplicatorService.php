<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\services;

use humhub\components\ActiveRecord;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\file\models\File;
use humhub\modules\file\models\FileContent;

class DuplicatorService
{
    protected CustomPage $sourcePage;
    protected CustomPage $targetPage;

    public function __construct(CustomPage $sourcePage, CustomPage $targetPage)
    {
        $this->sourcePage = $sourcePage;
        $this->targetPage = $targetPage;
    }

    public static function instance(CustomPage $sourcePage, CustomPage $targetPage): self
    {
        return new self($sourcePage, $targetPage);
    }

    /**
     * Set properties to new duplicating custom page before run the duplicating
     *
     * @return bool
     */
    public function beforeDuplicate(): bool
    {
        $this->targetPage->visibility_groups = $this->sourcePage->visibility_groups;
        $this->targetPage->visibility_languages = $this->sourcePage->visibility_languages;
        $this->targetPage->editors = $this->sourcePage->editors;

        foreach ($this->sourcePage->attributes as $attrKey => $attrValue) {
            if ($attrKey !== 'id') {
                $this->targetPage->$attrKey = $attrValue;
            }
        }

        foreach ($this->sourcePage->content->attributes as $attrKey => $attrValue) {
            if (!in_array($attrKey, ['id', 'guid', 'object_model', 'object_id', 'created_at', 'created_by', 'updated_at', 'updated_by'])) {
                $this->targetPage->content->$attrKey = $attrValue;
            }
        }

        return true;
    }

    /**
     * Run after the duplicating process
     *
     * @return void
     */
    public function afterDuplicate(): void
    {
        if (!empty($this->targetPage->url) && $this->targetPage->url === $this->sourcePage->url) {
            // Make URL unique
            $this->targetPage->updateAttributes(['url' => $this->targetPage->url . '-' . $this->targetPage->id]);
        }

        $this->duplicateAttachments();
    }

    /**
     * Duplicate all attached files from source to target Custom Page
     *
     * @return void
     */
    protected function duplicateAttachments(): void
    {
        foreach (File::findByRecord($this->sourcePage) as $sourceFile) {
            if ($newFile = self::duplicateFile($sourceFile, $this->targetPage)) {
                $this->targetPage->updateAttributes([
                    'page_content' => str_replace($sourceFile->guid, $newFile->guid, $this->targetPage->page_content),
                    'abstract' => str_replace($sourceFile->guid, $newFile->guid, $this->targetPage->abstract),
                ]);
            }
        }
    }

    public static function duplicateFile(File $sourceFile, ActiveRecord $targetRecord): ?File
    {
        $newFile = new FileContent();
        foreach ($sourceFile->attributes() as $attr) {
            if (!in_array($attr, ['id', 'guid', 'object_id', 'content_id', 'metadata', 'size', 'created_at', 'created_by', 'updated_at', 'updated_by', 'hash_sha1'])) {
                $newFile->$attr = $sourceFile->$attr;
            }
        }

        $newFile->object_id = $targetRecord->id;
        if ($targetRecord instanceof ContentActiveRecord) {
            $newFile->content_id = $targetRecord->content->id;
        }
        $newFile->newFileContent = file_get_contents($sourceFile->getStore()->get());

        return $newFile->validate() && $newFile->save() ? $newFile : null;
    }
}
