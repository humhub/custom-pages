<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\custom_pages\models\forms;


use Yii;
use yii\base\Model;

class SettingsForm extends Model
{
    const DEFAULT_VIEW_PATH_PAGES = '@custom_pages/views/custom/global_pages/';
    const DEFAULT_VIEW_PATH_SNIPPETS = '@custom_pages/views/custom/global_snippets/';
    const DEFAULT_VIEW_PATH_CONTAINER_PAGES = '@custom_pages/views/custom/container_pages/';
    const DEFAULT_VIEW_PATH_CONTAINER_SNIPPETS = '@custom_pages/views/custom/container_snippets/';

    /**
     * @var integer
     */
    public $phpPagesActive;

    /**
     * @var string
     */
    public $phpGlobalPagePath;

    /**
     * @var string
     */
    public $phpGlobalSnippetPath;

    /**
     * @var string
     */
    public $phpContainerSnippetPath;

    /**
     * @var string
     */
    public $phpContainerPagePath;

    /**
     * @var \humhub\components\SettingsManager
     */
    public $settings;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->settings = Yii::$app->getModule('custom_pages')->settings;
        $this->phpPagesActive = intval($this->settings->get('phpPagesActive', 0));
        $this->phpGlobalPagePath = $this->settings->get('phpGlobalPagePath', static::DEFAULT_VIEW_PATH_PAGES);
        $this->phpGlobalSnippetPath = $this->settings->get('phpGlobalSnippetPath', static::DEFAULT_VIEW_PATH_SNIPPETS);
        $this->phpContainerPagePath = $this->settings->get('phpContainerPagePath', static::DEFAULT_VIEW_PATH_CONTAINER_PAGES);
        $this->phpContainerSnippetPath = $this->settings->get('phpContainerSnippetPath', static::DEFAULT_VIEW_PATH_CONTAINER_SNIPPETS);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['phpPagesActive', 'integer'],
            [['phpGlobalPagePath', 'phpGlobalPagePath', 'phpGlobalSnippetPath', 'phpContainerSnippetPath', 'phpContainerPagePath'], 'validateViewPath'],
        ];
    }

    public function validateViewPath($attribute, $params)
    {
        if(!is_dir(Yii::getAlias($this->$attribute))) {
            $this->addError($attribute, Yii::t('CustomPagesModule.models_SettignsForm', 'The given view file path does not exist.'));
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'phpPagesActive' => Yii::t('CustomPagesModule.models_SettignsForm','Activate PHP based Pages and Snippets'),
            'phpGlobalPagePath' => Yii::t('CustomPagesModule.models_SettignsForm','PHP view path for global custom pages'),
            'phpGlobalSnippetPath' => Yii::t('CustomPagesModule.models_SettignsForm','PHP view path for global custom snippets'),
            'phpContainerPagePath' => Yii::t('CustomPagesModule.models_SettignsForm','PHP view path for custom space pages'),
            'phpContainerSnippetPath' => Yii::t('CustomPagesModule.models_SettignsForm','PHP view path for custom space snippets'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
            'phpPagesActive' => Yii::t('CustomPagesModule.models_SettignsForm','If disabled, existing php pages will still be online, but can\'t be created.'),
        ];
    }

    /**
     * Saves the settings in case the validation succeeds.
     */
    public function save()
    {
        if(!$this->validate()) {
            return false;
        }

        $this->settings->set('phpPagesActive', $this->phpPagesActive);

        if(empty($this->phpGlobalPagePath)) {
            $this->settings->delete('phpGlobalPagePath');
            $this->phpGlobalPagePath = static::DEFAULT_VIEW_PATH_PAGES;
        } else {
            $this->settings->set('phpGlobalPagePath', $this->phpGlobalPagePath);
        }

        if(empty($this->phpGlobalSnippetPath)) {
            $this->settings->delete('phpGlobalSnippetPath');
            $this->phpGlobalSnippetPath = static::DEFAULT_VIEW_PATH_SNIPPETS;
        } else {
            $this->settings->set('phpGlobalSnippetPath', $this->phpGlobalSnippetPath);
        }

        if(empty($this->phpContainerPagePath)) {
            $this->settings->delete('phpContainerPagePath');
            $this->phpContainerPagePath = static::DEFAULT_VIEW_PATH_SNIPPETS;
        } else {
            $this->settings->set('phpContainerPagePath', $this->phpContainerPagePath);
        }

        if(empty($this->phpContainerSnippetPath)) {
            $this->settings->delete('phpContainerSnippetPath');
            $this->phpContainerSnippetPath = static::DEFAULT_VIEW_PATH_CONTAINER_SNIPPETS;
        } else {
            $this->settings->set('phpContainerSnippetPath', $this->phpContainerSnippetPath);
        }

        return true;
    }
}