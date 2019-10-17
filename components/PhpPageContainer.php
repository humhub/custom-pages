<?php


namespace humhub\modules\custom_pages\components;

use HttpException;
use humhub\modules\custom_pages\models\CustomContentContainer;
use humhub\modules\custom_pages\models\forms\SettingsForm;
use humhub\modules\custom_pages\models\PhpType;
use humhub\modules\file\libs\FileHelper;
use Yii;
use yii\helpers\Html;

/**
 * @used-by CustomContentContainer
 */
trait PhpPageContainer
{

    /**
     * Additional validator for php based pages.
     *
     * @param $attribute
     * @param $params
     * @throws HttpException
     */
    public function validatePhpType($attribute, $params)
    {
        if(PhpType::isType($this->type)) {
            $settigns = new SettingsForm();
            if($this->isNewRecord && !$settigns->phpPagesActive) {
                throw new HttpException(403);
            }

            if(!$this->validatePhpViewFile()) {
                $this->addError($this->getPageContentProperty(), Yii::t('CustomPagesModule.components_Container', 'Invalid view file selection!'));
            }
        }
    }


    /**
     * Validates the view file setting for php based pages.
     *
     * @return bool
     */
    public function validatePhpViewFile()
    {
        $allowedFiles = $this->getAllowedPhpViewFileSelection();
        return array_key_exists(Html::getAttributeValue($this, $this->getPageContentProperty()), $allowedFiles);
    }

    /**
     * Returns the actual view file path for a php based page.
     *
     * @return bool|null|string
     */
    public function getPhpViewFilePath()
    {
        if(PhpType::isType($this->type)) {
            $viewFiles = $this->getAllowedPhpViewFileSelection(true);
            $viewName = Html::getAttributeValue($this, $this->getPageContentProperty());

            if(array_key_exists($viewName, $viewFiles)) {
                return $this->getPhpViewPathByView(basename($viewFiles[$viewName]), true);
            }
        }

        return null;
    }

    public function hasAllowedPhpViews()
    {
        return count($this->getAllowedPhpViewFileSelection()) > 0;
    }

    /**
     * Returns all allowed view files as associative array in the form of
     *
     *  [basename => file path] if $path = true
     *
     * or
     *
     *  [basename => basename] if $path = false
     *
     * @return string[]
     */
    public function getAllowedPhpViewFileSelection($path = false)
    {
        $settings = new SettingsForm();
        if(!$settings->phpPagesActive) {
            return [];
        }

        $files = FileHelper::findFiles($this->getPhpViewPathByView(), [
            'only' => ['*.php'],
            'recursive' => false
        ]);

        $result = [];
        foreach ($files as $file) {
            $baseName = basename($file, '.php');
            $result[$baseName] = ($path) ? $file : $baseName;
        }

        return $result;
    }

    /**
     * Returns the php view path.
     * @param string $view
     * @param bool $alias
     * @return bool|string
     */
    private function getPhpViewPathByView($view = '', $alias = false)
    {
        $path = rtrim($this->getPhpViewPath(), '/') . '/'.$view;
        return ($alias) ? $path : Yii::getAlias($path);
    }
}