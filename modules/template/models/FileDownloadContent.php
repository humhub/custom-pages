<?php

namespace humhub\modules\custom_pages\modules\template\models;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;
use humhub\modules\custom_pages\modules\template\widgets\TemplateContentFormFields;
use humhub\modules\file\models\File;
use humhub\modules\file\libs\FileHelper;

/**
 * @var $title string
 * @var $style string
 * @var $cssClass string
 * @var $showFileinfo integer
 * @var $showIcon integer
 */
class FileDownloadContent extends TemplateContentActiveRecord
{
    public static $label = 'File Download';
    
    public $file;

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'custom_pages_template_file_download_content';
    }
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        if($this->showFileinfo === null) {
            $this->showFileinfo = 1;
        }

        if($this->showIcon === null) {
            $this->showIcon = 1;
        }
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        $result = parent::rules();
        $result[] = [['file_guid'], 'required'];
        $result[] = [['title', 'style', 'cssClass'], 'string'];
        $result[] = [['showFileinfo', 'showIcon'], 'integer'];
        return $result;
    }
       
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        array_push($scenarios[self::SCENARIO_CREATE], 'file_guid', 'title', 'style', 'cssClass', 'showFileinfo', 'showIcon');
        array_push($scenarios[self::SCENARIO_EDIT_ADMIN], 'file_guid', 'title', 'style', 'cssClass', 'showFileinfo', 'showIcon');
        array_push($scenarios[self::SCENARIO_EDIT], 'file_guid', 'title', 'style', 'cssClass', 'showFileinfo', 'showIcon');
        return $scenarios;
    }
    
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return  [
            'file_guid' => Yii::t('CustomPagesModule.base', 'File'),
            'title' => Yii::t('CustomPagesModule.base', 'Title'),
            'style' => Yii::t('CustomPagesModule.base', 'Style'),
            'cssClass' => Yii::t('CustomPagesModule.base', 'Css Class'),
            'showFileinfo' => Yii::t('CustomPagesModule.base', 'Show additional file information (size)'),
            'showIcon' => Yii::t('CustomPagesModule.base', 'Add a file icon before the title')
        ];
    }
    
    public function saveFiles()
    {
        $files = File::findByRecord($this);

        foreach($files as $file) {
            if($file->guid !== $this->file_guid) {
                $file->delete();
            }
        }
        
        $this->fileManager->attach($this->file_guid);
    }
    
    public function getLabel()
    {
        return static::$label;
    }
    
    public function getFile()
    {
        return File::findOne(['guid' => $this->file_guid]);
    }
    
    public function hasFile()
    {
        return $this->file_guid != null && $this->getFile() != null;
    }
    
    public function getUrl()
    {
        $file = $this->getFile();
        return ($file != null) ? $file->getUrl() : null;
    }
    
    public function getDownloadUrl()
    {
        $file = $this->getFile();
        if($file) {
            return Url::to(['/file/file/download', 'guid' => $file->guid, 'download' => '1']);
        }
    }
    
    public function copy() {
        $clone = $this->createCopy();
        $clone->file_guid = $this->file_guid;
        $clone->title = $this->title;
        $clone->style = $this->style;
        $clone->cssClass = $this->cssClass;
        $clone->showFileinfo = $this->showFileinfo;
        $clone->showIcon = $this->showIcon;
        return $clone;
    }

    public function render($options = [])
    {   
        if($this->hasFile()) {
            $file =  $this->getFile();
            $options['htmlOptions'] = [
                'href' => $this->getDownloadUrl(),
                'style' => Html::encode($this->style),
                'class' => Html::encode($this->cssClass),
                'data-pjax-prevent' => '1'
            ];
            
            $content = ($this->title) ? $this->title : $file->file_name;
            $content = Html::encode($content);
            
            $fileInfo = FileHelper::getFileInfos($file);
            
            if($this->showIcon) {
                $options['htmlOptions']['class'] .= ' mime '.$fileInfo['mimeIcon'];
            }
            
            if($this->showFileinfo) {
                $content .= Html::tag('small', ' - '.$fileInfo['size_format'], ['class' => 'file-fileInfo']);
            }
            
            if($this->isEditMode($options)) {
                return $this->wrap('a', $content, $options);
            } else {
                return Html::tag('a', $content, $options['htmlOptions']);
            }
        }
        return '';
    }
    
    public function renderEmpty($options = [])
    {
        return '';
    }

    public function renderForm($form)
    {
        return TemplateContentFormFields::widget([
            'type' => 'fileDownload',
            'form' => $form,
            'model' => $this
        ]);
    }

}
