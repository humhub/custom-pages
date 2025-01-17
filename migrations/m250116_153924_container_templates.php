<?php

use humhub\components\Migration;
use humhub\modules\custom_pages\modules\template\models\Template;
use yii\db\Query;

class m250116_153924_container_templates extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $definitions = (new Query())
            ->select('id, dyn_attributes')
            ->from('custom_pages_template_element_content_definition')
            ->where(['LIKE', 'dyn_attributes', '"templates":[']);

        foreach ($definitions->each() as $definition) {
            $dynAttributes = json_decode($definition['dyn_attributes'], true);
            if (empty($dynAttributes['templates']) || !is_array($dynAttributes['templates'])) {
                continue;
            }

            $dynAttributes['templates'] = Template::find()
                ->select('name')
                ->where(['id' => $dynAttributes['templates']])
                ->column();

            $this->updateSilent(
                'custom_pages_template_element_content_definition',
                ['dyn_attributes' => json_encode($dynAttributes)],
                ['id' => $definition['id']],
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250116_153924_container_templates cannot be reverted.\n";

        return false;
    }
}
