<?php


use humhub\components\Migration;
use humhub\modules\custom_pages\models\CustomPage;

class m250722_084026_visibility extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->safeAddColumn('custom_pages_page', 'visibility', $this
            ->tinyInteger()
            ->notNull()
            ->defaultValue(CustomPage::VISIBILITY_PRIVATE)
            ->after('admin_only'));

        $this->db->createCommand('UPDATE custom_pages_page
            LEFT JOIN content ON content.object_model = :customPageClass
                  AND content.object_id = custom_pages_page.id
            SET custom_pages_page.visibility = content.visibility
            WHERE content.visibility = :publicVisibility', [
                'customPageClass' => CustomPage::class,
                'publicVisibility' => CustomPage::VISIBILITY_PUBLIC,
            ])->execute();

        $this->updateSilent(
            'custom_pages_page',
            ['visibility' => CustomPage::VISIBILITY_ADMIN],
            ['admin_only' => 1],
        );

        $this->safeDropColumn('custom_pages_page', 'admin_only');

        $this->safeCreateTable('custom_pages_page_setting', [
            'page_id' => $this->integer()->notNull(),
            'name' => $this->string(32)->notNull(),
            'value' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250722_084026_visibility cannot be reverted.\n";

        return false;
    }
}
