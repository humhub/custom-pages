<?php

use humhub\components\Migration;

use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\models\FileDownloadContent;
use humhub\modules\custom_pages\modules\template\models\ContainerContent;

class m170413_085114_download_link_template extends Migration
{
    public function up()
    {
        //Create Download Item
        $downloadItemTemplateId = $this->insertDownloadItemTemplate();
        $this->insertTemplateElement($downloadItemTemplateId, 'file_download', 'File', FileDownloadContent::className());
        
        //Create Download List
        $downloadListTemplateId = $this->insertDownloadListTemplate();
        $this->insertTemplateElement($downloadListTemplateId, 'download_list', 'File List', ContainerContent::className());
        
        //Create container definition for download_list container
        $this->insertSilent('custom_pages_template_container_content_definition', ['allow_multiple' => 1, 'is_inline' => 0, 'is_default' => 1]);
        
        $contentDefinitionId = $this->db->getLastInsertID();
        
        $this->insertSilent('custom_pages_template_container_content', ['definition_id' => $contentDefinitionId]);
        $this->insertSilent('custom_pages_template_owner_content', [
                'element_name' => 'download_list',
                'owner_model' => Template::className(),
                'owner_id' => $downloadListTemplateId,
                'content_type' => ContainerContent::className(),
                'content_id' => $this->db->getLastInsertID()
            ]);
        
        // Create allowed templates setting for download_list definition
        $this->insertSilent('custom_pages_template_container_content_template', [
            'definition_id' => $contentDefinitionId,
            'template_id' => $downloadItemTemplateId
        ]);
    }

    public function down()
    {
        echo "m170413_085114_download_link_template cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
    
    public function insertDownloadItemTemplate()
    {
        $this->insertSilent('custom_pages_template', [
            'name' => 'system_download_item',
            'engine' => 'twig',
            'description' => 'Single download item.',
            'source' => $this->getDownloadItemContent(), 
            'type' => Template::TYPE_CONTAINER,
            'created_at' => new \yii\db\Expression('NOW()')]);

        return $this->db->getLastInsertID();
    }
    
    public function getDownloadItemContent()
    {
        return <<< EOT
<div class="download-item">
    {{ file_download }}
</div>
EOT;
    }
    
    public function insertDownloadListTemplate()
    {
        $this->insertSilent('custom_pages_template', [
            'name' => 'system_download_list',
            'engine' => 'twig',
            'description' => 'File download list.',
            'source' => $this->getDownloadListContent(), 
            'type' => Template::TYPE_CONTAINER,
            'created_at' => new \yii\db\Expression('NOW()')]);

        return $this->db->getLastInsertID();
    }
    
    public function getDownloadListContent()
    {
        return <<< EOT
<div class="download-list">
    {{ download_list }}
</div>
EOT;
    }
    
    public function insertTemplateElement($tmplid, $name, $title, $contentType)
    {
        $this->insertSilent('custom_pages_template_element', [
            'template_id' => $tmplid,
            'name' => $name,
            'title' => $title,
            'content_type' => $contentType
        ]);
    }
}
