<?php

use humhub\components\Migration;
use yii\db\Query;

class m241220_101915_template_elements extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->safeCreateTable('custom_pages_template_element_content', [
            'id' => $this->primaryKey(),
            'element_id' => $this->integer()->notNull(),
            'dynAttributes' => $this->text(),
        ]);
        $this->safeAddForeignKey('fk-element_id', 'custom_pages_template_element_content', 'element_id', 'custom_pages_template_element', 'id', 'CASCADE');

        $this->migrateElements('custom_pages_template_text_content', 'Text', ['content', 'inline_text']);
        $this->migrateElements('custom_pages_template_richtext_content', 'Richtext', ['content']);
        $this->migrateElements('custom_pages_template_hh_richtext_content', 'HumHubRichtext', ['content']);
        $this->migrateElements('custom_pages_template_file_content', 'File', ['file_guid']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241220_101915_template_elements cannot be reverted.\n";

        return false;
    }

    private function migrateElements(string $oldTable, string $type, array $dynAttributes)
    {
        $oldContentType = 'humhub\\modules\\custom_pages\\modules\\template\\models\\' . $type . 'Content';
        $newContentType = 'humhub\\modules\\custom_pages\\modules\\template\\elements\\' . $type . 'Element';

        $elements = (new Query())
            ->select('ot.*, e.id AS elementId, oc.id AS ownerContentId')
            ->from($oldTable . ' AS ot')
            ->innerJoin('custom_pages_template_owner_content AS oc', 'ot.id = oc.content_id AND oc.content_type = :contentType', ['contentType' => $oldContentType])
            ->innerJoin('custom_pages_template_element AS e', 'e.content_type = oc.content_type AND e.name = oc.element_name');

        foreach ($elements->each() as $element) {
            $dynValues = [];
            foreach ($dynAttributes as $attribute) {
                if (isset($element[$attribute])) {
                    $dynValues[$attribute] = $element[$attribute];
                }
            }

            $this->insertSilent('custom_pages_template_element_content', [
                'element_id' => $element['elementId'],
                'dynAttributes' => json_encode($dynValues),
            ]);
            $newElementId = $this->db->getLastInsertID();

            $this->updateSilent(
                'custom_pages_template_owner_content',
                ['content_type' => $newContentType, 'content_id' => $newElementId],
                ['id' => $element['ownerContentId']],
            );

            $this->updateSilent(
                'custom_pages_template_element',
                ['content_type' => $newContentType],
                ['content_type' => $oldContentType],
            );

            $this->updateSilent(
                'file',
                ['object_model' => $newContentType, 'object_id' => $newElementId],
                ['object_model' => $oldContentType, 'object_id' => $element['id']],
            );
        }

        $this->safeDropTable($oldTable);
    }
}
