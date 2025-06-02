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
            'dyn_attributes' => $this->text(),
            'definition_id' => $this->integer(),
        ]);
        $this->safeCreateTable('custom_pages_template_element_content_definition', [
            'id' => $this->primaryKey(),
            'dyn_attributes' => $this->text(),
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

        // Migrate Container Elements:
        $this->renameTable('custom_pages_template_container_content_item', 'custom_pages_template_element_container_item');
        $this->safeDropForeignKey('fk-tmpl-container-item-content', 'custom_pages_template_element_container_item');
        $this->safeAddColumn('custom_pages_template_element_container_item', 'element_content_id', $this->integer()->after('container_content_id'));
        $this->migrateElements(
            'custom_pages_template_container_content',
            'Container',
            [],
            false,
            'custom_pages_template_container_content_definition',
            [
                'allow_multiple',
                'is_inline',
                'templates' => [
                    'table' => 'custom_pages_template_container_content_template',
                    'id' => 'definition_id',
                    'column' => 'template_id',
                ],
            ],
            [
                'custom_pages_template_element_container_item' => [
                    'old' => 'container_content_id',
                    'new' => 'element_content_id',
                ],
            ],
        );
        $this->safeDropColumn('custom_pages_template_element_container_item', 'container_content_id');
        $this->safeAddForeignKey('fk-tmpl-container-item-element-content', 'custom_pages_template_element_container_item', 'element_content_id', 'custom_pages_template_element_content', 'id', 'CASCADE');
        $this->safeDropTable('custom_pages_template_container_content_template');
        $this->safeDropTable('custom_pages_template_container_content');
        $this->safeDropTable('custom_pages_template_container_content_definition');
        $this->update(
            'custom_pages_template_owner_content',
            ['owner_model' => 'humhub\\modules\\custom_pages\\modules\\template\\elements\\ContainerItem'],
            ['owner_model' => 'humhub\\modules\\custom_pages\\modules\\template\\models\\ContainerContentItem'],
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241220_101915_template_elements cannot be reverted.\n";

        return false;
    }

    private function migrateElements(string $oldTable, string $type, array $dynAttributes, bool $deleteOldTable = true, ?string $oldDefinitionTable = null, ?array $definitionDynAttributes = null, ?array $updateLinkedElementContentTables = null)
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
                'dyn_attributes' => empty($dynValues) ? null : json_encode($dynValues),
                'definition_id' => $definitionId,
            ]);
            $newElementId = $this->db->getLastInsertID();

            $this->updateSilent(
                'custom_pages_template_owner_content',
                ['content_type' => $newContentType, 'content_id' => $newElementId],
                ['id' => $element['ownerContentId']],
            );

            $this->updateSilent(
                'file',
                ['object_model' => $newContentType, 'object_id' => $newElementId],
                ['object_model' => $oldContentType, 'object_id' => $element['id']],
            );

            $this->updateLinkedElementContentTables($element['id'], $newElementId, $updateLinkedElementContentTables);
        }

        $this->updateSilent(
            'custom_pages_template_element',
            ['content_type' => $newContentType],
            ['content_type' => $oldContentType],
        );

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
        foreach ($definitionDynAttributes as $attrKey => $attrName) {
            if (is_array($attrName)) {
                $definitionDynValues[$attrKey] = (new Query())
                    ->select($attrName['column'])
                    ->from($attrName['table'])
                    ->where([$attrName['id'] => $definition_id])
                    ->column();
            } elseif (isset($definition[$attrName])) {
                $definitionDynValues[$attrName] = $definition[$attrName];
            }
        }

        $this->insertSilent('custom_pages_template_element_content_definition', [
            'dyn_attributes' => empty($definitionDynValues) ? null : json_encode($definitionDynValues),
            'is_default' => $definition['is_default'],
        ]);

        return $this->db->getLastInsertID();
    }

    private function updateLinkedElementContentTables($oldElementContentId, $newElementContentId, ?array $tables = null)
    {
        if (!is_array($tables) || empty($oldElementContentId) || empty($newElementContentId)) {
            return;
        }

        foreach ($tables as $table => $columns) {
            $this->updateSilent(
                $table,
                [$columns['new'] => $newElementContentId],
                [$columns['old'] => $oldElementContentId],
            );
        }
    }
}
