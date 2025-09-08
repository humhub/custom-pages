<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\lib\templates\twig;

use humhub\helpers\ArrayHelper;
use humhub\modules\custom_pages\lib\templates\TemplateEngine;
use humhub\modules\custom_pages\Module;
use Twig\Environment;
use Twig\Extension\SandboxExtension;
use Twig\Extra\String\StringExtension;
use Yii;

/**
 * The TwigEngine is the default template eninge of this module and is used to
 * initializing the twig environment and rendering templates.
 *
 * @author buddha
 */
class TwigEngine implements TemplateEngine
{
    private static $sandboxExtensionConfig = [];

    /**
     * @inheritdoc
     *
     * @param string $template template name
     * @param array $content array input [elementName => content]
     * @return string
     */
    public function render($template, $content)
    {
        $loader = new DatabaseTwigLoader();
        $twig = new Environment($loader, ['autoescape' => false, 'debug' => true]);

        $securityPolicy = $this->getSecurityPolicy();
        if ($securityPolicy !== null) {
            $twig->addExtension(new SandboxExtension($securityPolicy, true));
            $twig->addExtension(new StringExtension());
            $twig->addExtension(new MarkdownExtension());
            $twig->addExtension(new YiiFormaterExtension());
        }
        return $twig->render($template, $content);
    }

    private function getSecurityPolicy(): ?SecurityPolicy
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('custom_pages');

        if (!$module->enableTwiqSandboxExtension) {
            return null;
        }



        return new SecurityPolicy(
            $module->enableTwiqSandboxExtensionConfig['allowedTags'],
            $module->enableTwiqSandboxExtensionConfig['allowedFilters'],
            ArrayHelper::merge(
                $module->enableTwiqSandboxExtensionConfig['allowedMethods'],
                static::$sandboxExtensionConfig['allowedMethods'] ?? [],
            ),
            $module->enableTwiqSandboxExtensionConfig['allowedProperties'],
            $module->enableTwiqSandboxExtensionConfig['allowedFunctions'],
        );
    }

    public static function registerSandboxExtensionAllowedFunctions($variableClassName, array $allowedFunctions)
    {
        static::$sandboxExtensionConfig['allowedMethods'][$variableClassName] = $allowedFunctions;
    }

}
