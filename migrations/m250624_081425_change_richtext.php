<?php

use humhub\components\Migration;

class m250624_081425_change_richtext extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameTemplateElement('HumHubRichtextElement', 'MarkdownElement');
        $this->renameTemplateElement('RichtextElement', 'HtmlElement');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250624_081425_change_richtext cannot be reverted.\n";

        return false;
    }

    private function renameTemplateElement(string $oldName, string $newName)
    {
        $this->updateSilent(
            'custom_pages_template_element',
            ['content_type' => 'humhub\\modules\\custom_pages\\modules\\template\\elements\\' . $newName],
            ['content_type' => 'humhub\\modules\\custom_pages\\modules\\template\\elements\\' . $oldName],
        );

        $this->updateSilent(
            'file',
            ['object_model' => 'humhub\\modules\\custom_pages\\modules\\template\\elements\\' . $newName],
            ['object_model' => 'humhub\\modules\\custom_pages\\modules\\template\\elements\\' . $oldName],
        );
    }
}
