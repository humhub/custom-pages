<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\services;

use humhub\modules\custom_pages\models\CustomPage;
use Yii;
use yii\db\Query;

class SettingService
{
    private const string TABLE = 'custom_pages_page_setting';
    protected CustomPage $page;

    public function __construct(CustomPage $page)
    {
        $this->page = $page;
    }

    public function getAll(string $name): array
    {
        if ($this->page->isNewRecord) {
            return [];
        }

        return (new Query())
            ->select('value')
            ->from(self::TABLE)
            ->where(['page_id' => $this->page->id])
            ->andWhere(['name' => $name])
            ->column();
    }

    public function update(string $name, $values): void
    {
        if ($this->page->isNewRecord) {
            return;
        }

        $this->delete($name);

        if ((is_string($values) || is_int($values)) && $values !== '') {
            $values = [$values];
        }

        if (is_array($values) && $values !== []) {
            $newRecords = [];
            foreach ($values as $value) {
                $newRecords[] = [$this->page->id, $name, $value];
            }

            Yii::$app->db->createCommand()
                ->batchInsert(self::TABLE, ['page_id', 'name', 'value'], $newRecords)
                ->execute();
        }
    }

    public function delete(string $name): void
    {
        Yii::$app->db->createCommand()->delete(self::TABLE, [
            'page_id' => $this->page->id,
            'name' => $name,
        ])->execute();
    }
}
