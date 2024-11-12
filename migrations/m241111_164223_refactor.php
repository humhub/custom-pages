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
        $this->moveOldRecords('custom_pages_snippet', 'Snippet');
        $this->moveOldRecords('custom_pages_container_snippet', 'ContainerSnippet');
        $this->moveOldRecords('custom_pages_container_page', 'ContainerPage');

        $this->safeCreateIndex('idx_target', 'custom_pages_page', 'target');
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

    private function moveOldRecords(string $oldTableName, string $oldClassName): void
    {
        $records = (new Query())->select('*')->from($oldTableName);

        foreach ($records->each() as $record) {
            $oldId = $record['id'];
            unset($record['id']);
            if ($record['target'] === 'Dasboard') {
                $record['target'] = 'Dashboard';
            }

            $this->insert('custom_pages_page', $record);
            $this->updateRelatedObjectRecords([
                'object_model' => 'humhub\\modules\\custom_pages\\models\\' . $oldClassName,
                'object_id' => $oldId,
            ], [
                'object_model' => 'humhub\\modules\\custom_pages\\models\\Page',
                'object_id' => $this->db->lastInsertID,
            ]);
        }

        $this->safeDropTable($oldTableName);
    }
}
