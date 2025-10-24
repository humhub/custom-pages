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
    public const TABLE = 'custom_pages_page_setting';

    public function __construct(protected CustomPage $page)
    {
    }

    /**
     * Get all values of the page setting by name
     *
     * @param string $name
     * @return array
     */
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

    /**
     * Update custom page setting with new value(s)
     *
     * @param string $name
     * @param array|string|int $values
     * @return void
     */
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

    /**
     * Delete all values of the page setting by name
     *
     * @param string $name
     * @return void
     */
    public function delete(string $name): void
    {
        Yii::$app->db->createCommand()->delete(self::TABLE, [
            'page_id' => $this->page->id,
            'name' => $name,
        ])->execute();
    }

    /**
     * Check the page setting has at least one requested value
     *
     * @param string $name Setting name
     * @param array|string|int $values Values for checking
     * @param bool $returnOnEmpty When the setting is empty then return this as result by default
     * @return bool
     */
    public function has(string $name, $checkValues, bool $returnOnEmpty = true): bool
    {
        $storedValues = $this->getAll($name);
        if ($storedValues === []) {
            return $returnOnEmpty;
        }

        if (is_scalar($checkValues) && $checkValues !== '') {
            $checkValues = [$checkValues];
        }

        if (is_array($checkValues) && $checkValues !== []) {
            foreach ($storedValues as $storedValue) {
                if (in_array($storedValue, $checkValues)) {
                    return true;
                }
            }
        }

        return false;
    }
}
