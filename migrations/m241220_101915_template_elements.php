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
            'fields' => $this->text(),
        ]);
        $this->safeAddForeignKey('fk-element_id', 'custom_pages_template_element_content', 'element_id', 'custom_pages_template_element', 'id', 'CASCADE');

        $this->migrateTextElements();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241220_101915_template_elements cannot be reverted.\n";

        return false;
    }

    private function migrateTextElements()
    {
        $oldContentType = 'humhub\\modules\\custom_pages\\modules\\template\\models\\TextContent';
        $newContentType = 'humhub\\modules\\custom_pages\\modules\\template\\elements\\TextElement';

        $textElements = (new Query())
            ->select('tc.*, e.id AS elementId, oc.id AS ownerContentId')
            ->from('custom_pages_template_text_content AS tc')
            ->leftJoin('custom_pages_template_owner_content AS oc', 'tc.id = oc.content_id AND oc.content_type = :contentType', ['contentType' => $oldContentType])
            ->leftJoin('custom_pages_template_element AS e', 'e.content_type = oc.content_type AND e.name = oc.element_name');

        foreach ($textElements->each() as $text) {
            $this->insertSilent('custom_pages_template_element_content', [
                'element_id' => $text['elementId'],
                'fields' => json_encode([
                    'content' => $text['content'],
                    'inline_text' => $text['inline_text'],
                ]),
            ]);

            $this->updateSilent(
                'custom_pages_template_owner_content',
                ['content_type' => $newContentType, 'content_id' => $this->db->getLastInsertID()],
                ['id' => $text['ownerContentId']],
            );

            $this->updateSilent(
                'custom_pages_template_element',
                ['content_type' => $newContentType],
                ['content_type' => $oldContentType],
            );
        }

        $this->safeDropTable('custom_pages_template_text_content');
    }
}
