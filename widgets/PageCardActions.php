<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\widgets;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\custom_pages\helpers\PageType;
use humhub\modules\custom_pages\helpers\Url;
use humhub\widgets\menu\CardHeaderActions;
use humhub\widgets\menu\MenuLink;
use Yii;

/**
 * The header actions of the page table ({@see PageTable}): "+" to add a page or snippet, which
 * asks for its target first.
 */
class PageCardActions extends CardHeaderActions
{
    /**
     * @inheritdoc
     */
    public $id = 'custom-pages-page-card-actions';

    /**
     * @var string the page type, {@see PageType::Page} or {@see PageType::Snippet}
     */
    public string $pageType = PageType::Page;

    /**
     * @var ContentContainerActiveRecord|null the space whose pages are listed
     */
    public ?ContentContainerActiveRecord $contentContainer = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->addEntry(new MenuLink([
            'id' => 'add-' . $this->pageType,
            'label' => $this->pageType === PageType::Snippet
                ? Yii::t('CustomPagesModule.base', 'Add snippet')
                : Yii::t('CustomPagesModule.base', 'Add page'),
            'url' => Url::toChooseTarget($this->pageType, $this->contentContainer),
            'icon' => 'plus',
            'isPrimary' => true,
            'htmlOptions' => ['data-action-click' => 'ui.modal.load'],
            'sortOrder' => 100,
        ]));

        parent::init();
    }
}
