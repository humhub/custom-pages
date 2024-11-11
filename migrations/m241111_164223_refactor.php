<?php

use humhub\components\Migration;
use yii\db\Query;

/**
 * Class m241111_164223_refactor
 */
class m241111_164223_refactor extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->safeAddColumn('custom_pages_page', 'is_snippet', $this->boolean()->notNull()->defaultValue(0)->after('id'));
        $this->safeCreateIndex('idx_is_snippet', 'custom_pages_page', 'is_snippet');

        // Move Global Snippets into the Page table
        $snippets = (new Query())->select('*')->from('custom_pages_snippet');
        foreach ($snippets->each() as $snippet) {
            $oldSnippetId = $snippet['id'];
            unset($snippet['id']);
            if ($snippet['target'] === 'Dasboard') {
                $snippet['target'] = 'Dashboard';
            }

            $this->insert('custom_pages_page', ['is_snippet' => 1] + $snippet);
            $this->updateRelatedObjectRecords([
                'object_model' => 'humhub\\modules\\custom_pages\\models\\Snippet',
                'object_id' => $oldSnippetId,
            ], [
                'object_model' => 'humhub\\modules\\custom_pages\\models\\Page',
                'object_id' => $this->db->lastInsertID,
            ]);
        }
//        $this->safeDropTable('custom_pages_snippet');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
//        echo "m241111_164223_refactor cannot be reverted.\n";
//
//        return false;
    }

    private function updateRelatedObjectRecords(array $oldObjectData, array $newObjectData): void
    {
        $tables = [
            'custom_pages_template_container',
            'content',
            'comment',
            'like',
            'file',
        ];

        foreach ($tables as $table) {
            $this->update($table, $newObjectData, $oldObjectData);
        }
    }
}
