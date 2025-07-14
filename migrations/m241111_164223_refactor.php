<?php

use humhub\components\Migration;
use yii\db\Expression;
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
        $this->updateRelatedObjectRecords(
            ['object_model' => 'humhub\\modules\\custom_pages\\models\\Page'],
            ['object_model' => 'humhub\\modules\\custom_pages\\models\\CustomPage'],
        );

        $this->safeAddColumn('custom_pages_page', 'old_id', $this->integer());
        $this->update('custom_pages_page', ['old_id' => new Expression('id')]);

        $this->moveOldRecords('custom_pages_snippet', 'Snippet');
        $this->moveOldRecords('custom_pages_container_snippet', 'ContainerSnippet');
        $this->moveOldRecords('custom_pages_container_page', 'ContainerPage');

        $this->safeCreateIndex('idx_target', 'custom_pages_page', 'target');

        // Modify the columns 'object_model' and 'object_id' to 'page_id',
        // because only the object CustomPage is used there.
        $this->safeDropColumn('custom_pages_template_container', 'object_model');
        $this->renameColumn('custom_pages_template_container', 'object_id', 'page_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241111_164223_refactor cannot be reverted.\n";

        return false;
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
            $record['old_id'] = $record['id'];
            unset($record['id']);

            if ($record['target'] === 'Dasboard') {
                $record['target'] = 'Dashboard';
            }

            $this->insert('custom_pages_page', $record);
            $this->updateRelatedObjectRecords([
                'object_model' => 'humhub\\modules\\custom_pages\\models\\' . $oldClassName,
                'object_id' => $record['old_id'],
            ], [
                'object_model' => 'humhub\\modules\\custom_pages\\models\\CustomPage',
                'object_id' => $this->db->lastInsertID,
            ]);
        }

        $this->safeDropTable($oldTableName);
    }
}
