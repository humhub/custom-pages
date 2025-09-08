<?php

use humhub\components\Migration;

class m250129_182425_fix_element_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $types = [
            'Text',
            'Richtext',
            'HumHubRichtext',
            'File',
            'FileDownload',
            'Rss',
            'User',
            'Space',
            'Users',
            'Spaces',
            'Image',
            'Container',
        ];

        foreach ($types as $type) {
            $this->update(
                'custom_pages_template_element',
                ['content_type' => 'humhub\\modules\\custom_pages\\modules\\template\\elements\\' . $type . 'Element'],
                ['content_type' => 'humhub\\modules\\custom_pages\\modules\\template\\models\\' . $type . 'Content'],
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250129_182425_fix_element_type cannot be reverted.\n";

        return false;
    }
}
