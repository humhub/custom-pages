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
            'dynAttributes' => $this->text(),
            'definition_id' => $this->integer(),
        ]);
        $this->safeCreateTable('custom_pages_template_element_content_definition', [
            'id' => $this->primaryKey(),
            'dynAttributes' => $this->text(),
            'is_default' => $this->boolean()->notNull()->defaultValue(0),
        ]);
        $this->safeAddForeignKey('fk-element_id', 'custom_pages_template_element_content', 'element_id', 'custom_pages_template_element', 'id', 'CASCADE');
        $this->safeAddForeignKey('fk-definition_id', 'custom_pages_template_element_content', 'definition_id', 'custom_pages_template_element_content_definition', 'id', 'CASCADE');

        $this->migrateElements('custom_pages_template_text_content', 'Text', ['content', 'inline_text']);
        $this->migrateElements('custom_pages_template_richtext_content', 'Richtext', ['content']);
        $this->migrateElements('custom_pages_template_hh_richtext_content', 'HumHubRichtext', ['content']);
        $this->migrateElements('custom_pages_template_file_content', 'File', ['file_guid']);
        $this->migrateElements('custom_pages_template_file_download_content', 'FileDownload', ['file_guid', 'title', 'style', 'cssClass', 'showFileinfo', 'showIcon']);
        $this->migrateElements('custom_pages_template_rss_content', 'Rss', ['url', 'cache_time', 'limit']);
        $this->migrateElements('custom_pages_template_contentcontainer_content', 'User', ['guid'], false);
        $this->migrateElements('custom_pages_template_contentcontainer_content', 'Space', ['guid']);
        $this->migrateElements('custom_pages_template_records_content', 'Users', ['type', 'options' => 'jsonMerge'], false);
        $this->migrateElements('custom_pages_template_records_content', 'Spaces', ['type', 'options' => 'jsonMerge']);
        $this->migrateElements('custom_pages_template_image_content', 'Image', ['file_guid', 'alt'], true, 'custom_pages_template_image_content_definition', ['height', 'width', 'style']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241220_101915_template_elements cannot be reverted.\n";

        return false;
    }

    private function migrateElements(string $oldTable, string $type, array $dynAttributes, bool $deleteOldTable = true, ?string $oldDefinitionTable = null, ?array $definitionDynAttributes = null)
    {
        $oldContentType = 'humhub\\modules\\custom_pages\\modules\\template\\models\\' . $type . 'Content';
        $newContentType = 'humhub\\modules\\custom_pages\\modules\\template\\elements\\' . $type . 'Element';

        $elements = (new Query())
            ->select('ot.*, e.id AS elementId, oc.id AS ownerContentId')
            ->from($oldTable . ' AS ot')
            ->innerJoin('custom_pages_template_owner_content AS oc', 'ot.id = oc.content_id AND oc.content_type = :contentType', ['contentType' => $oldContentType])
            ->innerJoin('custom_pages_template_element AS e', 'e.content_type = oc.content_type AND e.name = oc.element_name');

        // Map between old and new definition Ids; Key - old, Value - new.
        $definitionIds = [];

        foreach ($elements->each() as $element) {
            $definitionId = null;
            if (!empty($element['definition_id'])) {
                if (!isset($definitionIds[$element['definition_id']])) {
                    $definitionIds[$element['definition_id']] = $this->migrateDefinition($element['definition_id'], $oldDefinitionTable, $definitionDynAttributes);
                }
                $definitionId = $definitionIds[$element['definition_id']];
            }

            $dynValues = [];
            foreach ($dynAttributes as $attrKey => $attrName) {
                if ($attrName === 'jsonMerge') {
                    if (!empty($element[$attrKey])) {
                        $jsonValues = @json_decode($element[$attrKey], true);
                        if (is_array($jsonValues)) {
                            $dynValues = array_merge($dynValues, $jsonValues);
                        }
                    }
                } elseif (isset($element[$attrName])) {
                    $dynValues[$attrName] = $element[$attrName];
                }
            }

            $this->insertSilent('custom_pages_template_element_content', [
                'element_id' => $element['elementId'],
                'dynAttributes' => json_encode($dynValues),
                'definition_id' => $definitionId,
            ]);
            $newElementId = $this->db->getLastInsertID();

            $this->updateSilent(
                'custom_pages_template_owner_content',
                ['content_type' => $newContentType, 'content_id' => $newElementId],
                ['id' => $element['ownerContentId']],
            );

            $this->updateSilent(
                'custom_pages_template_element',
                ['content_type' => $newContentType],
                ['content_type' => $oldContentType],
            );

            $this->updateSilent(
                'file',
                ['object_model' => $newContentType, 'object_id' => $newElementId],
                ['object_model' => $oldContentType, 'object_id' => $element['id']],
            );
        }

        if ($deleteOldTable) {
            $this->safeDropTable($oldTable);
            if ($oldDefinitionTable !== null) {
                $this->safeDropTable($oldDefinitionTable);
            }
        }
    }

    private function migrateDefinition(?int $definition_id, ?string $oldDefinitionTable = null, ?array $definitionDynAttributes = null): ?int
    {
        if ($oldDefinitionTable === null || $definitionDynAttributes === null || empty($definition_id)) {
            return null;
        }

        $definition = (new Query())
            ->select('*')
            ->from($oldDefinitionTable)
            ->where(['id' => $definition_id])
            ->one();

        if (!$definition) {
            return null;
        }

        $definitionDynValues = [];
        foreach ($definitionDynAttributes as $attrName) {
            if (isset($definition[$attrName])) {
                $definitionDynValues[$attrName] = $definition[$attrName];
            }
        }

        $this->insertSilent('custom_pages_template_element_content_definition', [
            'dynAttributes' => json_encode($definitionDynValues),
            'is_default' => $definition['is_default'],
        ]);

        return $this->db->getLastInsertID();
    }
}
