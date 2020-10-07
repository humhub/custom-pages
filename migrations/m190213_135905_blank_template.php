<?php

use humhub\components\Migration;
use humhub\modules\custom_pages\modules\template\models\ContainerContent;
use humhub\modules\custom_pages\modules\template\models\Template;

/**
 * Class m190213_135902_align_page_types
 */
class m190213_135905_blank_template extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insertSilent('custom_pages_template', [
            'name' => 'system_plain_layout',
            'engine' => 'twig',
            'description' => 'Simple container layout.',
            'source' => $this->getSourceSource(),
            'type' => Template::TYPE_LAYOUT,
            'created_at' => date('Y-m-d G:i:s')]);

        $tempalteId = $this->db->getLastInsertID();

        // Insert elements
        $this->insertTemplateElement($tempalteId, 'content', ContainerContent::class);
        $this->insertSilent('custom_pages_template_container_content_definition', ['allow_multiple' => 1, 'is_inline' => 0, 'is_default' => 1]);
        $this->insertSilent('custom_pages_template_container_content', ['definition_id' => $this->db->getLastInsertID()]);
        $this->insertSilent('custom_pages_template_owner_content', [
            'element_name' => 'content',
            'owner_model' => Template::class,
            'owner_id' => $tempalteId,
            'content_type' => ContainerContent::class,
            'content_id' => $this->db->getLastInsertID()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190213_135902_align_page_types cannot be reverted.\n";

        return false;
    }

    public function insertTemplateElement($tmplid, $name, $contentType)
    {
        $this->insertSilent('custom_pages_template_element', [
            'template_id' => $tmplid,
            'name' => $name,
            'content_type' => $contentType
        ]);
    }

    public function getSourceSource()
    {
        return <<< EOT
{{ content }}
EOT;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190213_135902_align_page_types cannot be reverted.\n";

        return false;
    }
    */
}
