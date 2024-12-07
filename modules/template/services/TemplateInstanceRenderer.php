<?php

namespace humhub\modules\custom_pages\modules\template\services;

use humhub\modules\custom_pages\lib\templates\TemplateEngineFactory;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use Yii;
use yii\caching\DummyCache;

class TemplateInstanceRendererService
{
    const CACHE_PREFIX = 'cp-';

    private $templateInstance;

    private TemplateService $templateService;

    public function __construct(TemplateInstance $templateInstance)
    {
        $this->templateInstance = $templateInstance;
        $this->templateService = new TemplateService($this->templateInstance->template);
    }

    public function render()
    {
        $cache = ($this->isCachable()) ? Yii::$app->cache : new DummyCache();

        return $cache->getOrSet(static::CACHE_PREFIX . $this->templateInstance->id, function () {
            $engine = TemplateEngineFactory::create("twig");

            $twigVariables = [];
            foreach ($this->templateService->getElements() as $element) {
                $twigVariables[$element->getTemplateName()] = $element->getTemplateValue($this->templateInstance);
            }

            return $engine->render($this->templateInstance->template->source, $twigVariables);
        });
    }

    private function isCachable(): bool
    {
        foreach ($this->templateService->getElements() as $element) {
            if ($element->isDynamic()) {
                return false;
            }
        }

        return true;
    }

}