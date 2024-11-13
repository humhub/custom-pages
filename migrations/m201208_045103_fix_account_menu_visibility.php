<?php

use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\helpers\PageType;
use yii\db\Migration;

/**
 * Class m201208_045103_fix_account_menu_visibility
 */
class m201208_045103_fix_account_menu_visibility extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Fix public access("Members & Guests") of custom pages with type "User Account Menu (Settings)"
        // to private access("Members only") because guests cannot see such pages at all:
        Yii::$app->db->createCommand('UPDATE `content`
            INNER JOIN `custom_pages_page` ON custom_pages_page.id = content.object_id
              SET content.visibility = :visibility_private
            WHERE content.object_model = :object_model
              AND custom_pages_page.target = :target
              AND content.visibility = :visibility_public')
            ->bindValues([
                ':visibility_private' => CustomPage::VISIBILITY_PRIVATE,
                ':visibility_public' => CustomPage::VISIBILITY_PUBLIC,
                ':object_model' => CustomPage::class,
                ':target' => PageType::TARGET_ACCOUNT_MENU,
            ])
            ->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201208_045103_fix_account_menu_visibility cannot be reverted.\n";

        return false;
    }
}
