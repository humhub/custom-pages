<?php

namespace humhub\modules\custom_pages\models;

use humhub\modules\custom_pages\models\forms\SettingsForm;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\space\models\Space;
use Yii;

/**
 * This is the model class for table "custom_pages_container_snipped".
 *
 * ContainerSnippets are snippets which can be added to a space sidebar.
 *
 * The followings are the available columns in table 'custom_pages_container_page':
 * @property int $id
 * @property int $type
 * @property string $title
 * @property string $icon
 * @property string $page_content
 * @property string $iframe_attrs
 * @property int $sort_order
 * @property int $admin_only
 * @property string $cssClass
 */
class ContainerSnippet extends Page
{
    /**
     * @inheritdoc
     */
    public function getVisibilitySelection()
    {
        $result = [
            static::VISIBILITY_ADMIN_ONLY => Yii::t('CustomPagesModule.base', 'Admin only'),
            static::VISIBILITY_PRIVATE => Yii::t('CustomPagesModule.base', 'Space Members only'),
        ];

        $container = $this->content->container;
        if ($container->visibility != Space::VISIBILITY_NONE) {
            $result[static::VISIBILITY_PUBLIC] = Yii::t('CustomPagesModule.base', 'Public');
        }

        return $result;
    }
}
