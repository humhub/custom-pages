<?php

use humhub\components\Migration;
use humhub\modules\custom_pages\models\CustomPage;
use yii\db\Query;

/**
 * Restores the Space/Profile "Homepage" settings (`indexUrl`, `indexGuestUrl`) which pointed
 * to the old IDs of container pages before they got new IDs in `m241111_164223_refactor`.
 */
class m261022_110000_fix_space_index_url extends Migration
{
    private const SETTING_NAMES = ['indexUrl', 'indexGuestUrl'];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settings = (new Query())
            ->select(['module_id', 'contentcontainer_id', 'name', 'value'])
            ->from('contentcontainer_setting')
            ->where(['name' => self::SETTING_NAMES])
            ->andWhere(['like', 'value', 'custom_pages'])
            ->all();

        foreach ($settings as $setting) {
            if (!preg_match('/([?&]id=)(\d+)(?=&|$)/', (string) $setting['value'], $match)) {
                continue;
            }

            $containerId = (int) $setting['contentcontainer_id'];
            $pageId = (int) $match[2];

            // The link refers to the old ID only if a page of this container had it before the refactoring
            $newPageId = $this->findPageId($containerId, ['old_id' => $pageId]);
            if ($newPageId === null || $newPageId === $pageId) {
                continue;
            }

            $this->update('contentcontainer_setting', [
                'value' => preg_replace('/([?&]id=)\d+(?=&|$)/', '${1}' . $newPageId, (string) $setting['value']),
            ], [
                'module_id' => $setting['module_id'],
                'contentcontainer_id' => $containerId,
                'name' => $setting['name'],
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m261022_110000_fix_space_index_url cannot be reverted.\n";

        return false;
    }

    private function findPageId(int $containerId, array $condition): ?int
    {
        $id = (new Query())
            ->select('custom_pages_page.id')
            ->from('custom_pages_page')
            ->innerJoin('content', 'content.object_model = :class AND content.object_id = custom_pages_page.id', [
                'class' => CustomPage::class,
            ])
            ->where(['content.contentcontainer_id' => $containerId])
            ->andWhere($this->prefixColumns($condition))
            ->scalar();

        return $id === false ? null : (int) $id;
    }

    private function prefixColumns(array $condition): array
    {
        $result = [];
        foreach ($condition as $column => $value) {
            $result['custom_pages_page.' . $column] = $value;
        }

        return $result;
    }
}
