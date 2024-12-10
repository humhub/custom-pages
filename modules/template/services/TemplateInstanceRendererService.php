<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\services;

use humhub\modules\custom_pages\helpers\Html;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\modules\template\components\TemplateCache;
use humhub\modules\custom_pages\modules\template\models\PagePermission;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use yii\web\NotFoundHttpException;

class TemplateInstanceRendererService
{
    private ?TemplateInstance $templateInstance = null;
    private bool $applyScriptNonce = true;
    private bool $ignoreCache = false;

    public function __construct(CustomPage $customPage)
    {
        if (!empty($customPage->id)) {
            $this->templateInstance = TemplateInstance::findOne(['page_id' => $customPage->id]);
        }

        if ($this->templateInstance === null) {
            throw new NotFoundHttpException('Template instance is not found!');
        }
    }

    public static function instance(CustomPage $customPage): self
    {
        return new self($customPage);
    }

    public function disableScriptNonce(): self
    {
        $this->applyScriptNonce = false;
        return $this;
    }

    public function ignoreCache(): self
    {
        $this->ignoreCache = true;
        return $this;
    }

    /**
     * Render the template content
     *
     * @param bool $editMode
     * @return string
     */
    public function render($editMode = false): string
    {
        $editMode = $editMode && PagePermission::canEdit();

        if (!$this->ignoreCache && !$editMode && TemplateCache::exists($this->templateInstance)) {
            $html = TemplateCache::get($this->templateInstance);
        } else {
            $html = $this->templateInstance->render($editMode);
            if (!$editMode) {
                TemplateCache::set($this->templateInstance, $html);
            }
        }

        if ($this->applyScriptNonce) {
            $html = Html::applyScriptNonce($html);
        }

        return $html;
    }
}
