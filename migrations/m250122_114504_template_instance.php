<?php

use humhub\components\Migration;
use yii\db\Expression;
use yii\db\Query;

class m250122_114504_template_instance extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->safeAddColumn('custom_pages_template_instance', 'container_item_id', $this->integer()->unsigned());
        $this->db->createCommand(
            'INSERT INTO custom_pages_template_instance (container_item_id, template_id, page_id)
            SELECT item.id, item.template_id, instance.page_id
            FROM custom_pages_template_element_container_item AS item
            LEFT JOIN custom_pages_template_owner_content AS owner
                   ON item.element_content_id = owner.content_id
                  AND owner.content_type = :containerElementClass
                  AND owner.owner_model = :templateInstanceClass
            LEFT JOIN custom_pages_template_instance AS instance
                   ON owner.owner_id = instance.id
            ORDER BY item.id ASC',
            [
                'containerElementClass' => 'humhub\\modules\\custom_pages\\modules\\template\\elements\\ContainerElement',
                'templateInstanceClass' => 'humhub\\modules\\custom_pages\\modules\\template\\models\\TemplateInstance',
            ],
        )->execute();

        $containerItems = (new Query())
            ->select('id')
            ->from('custom_pages_template_instance')
            ->where(['IS NOT', 'container_item_id', new Expression('NULL')])
            ->indexBy('container_item_id')
            ->column();

        $this->safeAddColumn('custom_pages_template_element_content', 'template_instance_id', $this->integer()->unsigned());

        $owners = (new Query())
            ->select('content_id, owner_model, owner_id')
            ->from('custom_pages_template_owner_content')
            ->where(['!=', 'owner_model', 'humhub\\modules\\custom_pages\\modules\\template\\models\\Template']);

        foreach ($owners->each() as $owner) {
            $templateInstanceId = match ($owner['owner_model']) {
                'humhub\\modules\\custom_pages\\modules\\template\\models\\TemplateInstance' => $owner['owner_id'],
                'humhub\\modules\\custom_pages\\modules\\template\\elements\\ContainerItem' => $containerItems[$owner['owner_id']] ?? null,
                default => null,
            };

            if ($templateInstanceId !== null) {
                $this->updateSilent(
                    'custom_pages_template_element_content',
                    ['template_instance_id' => $templateInstanceId],
                    ['id' => $owner['content_id']],
                );
            }
        }

        $this->safeDropForeignKey('fk-tmpl-container-item-tmpl', 'custom_pages_template_element_container_item');
        $this->safeDropColumn('custom_pages_template_element_container_item', 'template_id');

        $this->safeDropTable('custom_pages_template_owner_content');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250122_114504_template_instance cannot be reverted.\n";

        return false;
    }
}
