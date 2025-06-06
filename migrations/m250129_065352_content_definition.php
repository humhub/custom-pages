<?php

use humhub\components\Migration;
use yii\db\Query;

class m250129_065352_content_definition extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('custom_pages_template_instance', 'container_item_id', $this->integer());
        $this->safeAddForeignKey('fk-container_item_id', 'custom_pages_template_instance', 'container_item_id', 'custom_pages_template_element_container_item', 'id', 'CASCADE');
        $this->safeAddForeignKey('fk-page_id', 'custom_pages_template_instance', 'page_id', 'custom_pages_page', 'id', 'CASCADE');

        $this->alterColumn('custom_pages_template_element_content', 'template_instance_id', $this->integer());
        $this->safeAddForeignKey('fk-template_instance_id', 'custom_pages_template_element_content', 'template_instance_id', 'custom_pages_template_instance', 'id', 'CASCADE');

        $this->safeAddColumn('custom_pages_template_element', 'dyn_attributes', $this->text());

        $definitions = (new Query())
            ->select('c.element_id, d.dyn_attributes')
            ->from('custom_pages_template_element_content AS c')
            ->innerJoin('custom_pages_template_element_content_definition AS d', 'c.definition_id = d.id')
            ->where(['template_instance_id' => null]);

        foreach ($definitions->each() as $definition) {
            $this->updateSilent(
                'custom_pages_template_element',
                ['dyn_attributes' => $definition['dyn_attributes']],
                ['id' => $definition['element_id']],
            );
        }

        $this->safeDropForeignKey('fk-definition_id', 'custom_pages_template_element_content');
        $this->safeDropColumn('custom_pages_template_element_content', 'definition_id');
        $this->safeDropTable('custom_pages_template_element_content_definition');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250129_065352_content_definition cannot be reverted.\n";

        return false;
    }

    /**
     * @inheritdoc
     */
    protected function safeAddForeignKey(string $index, string $table, $columns, string $refTable, $refColumns, ?string $delete = null, ?string $update = null)
    {
        if (is_string($columns) && is_string($refColumns) && !str_contains($columns . $refColumns, ',')) {
            // Delete wrong records if they haven't got a linked record in the ref table
            $this->db
                ->createCommand()
                ->delete($table, $this->db->quoteColumnName($columns) . ' IS NOT NULL AND ' .
                    $this->db->quoteColumnName($columns) . ' NOT IN (SELECT ' . $this->db->quoteColumnName($refColumns) . ' FROM ' . $this->db->quoteTableName($refTable) . ')')
                ->execute();
        }

        return parent::safeAddForeignKey($index, $table, $columns, $refTable, $refColumns, $delete, $update);
    }
}
