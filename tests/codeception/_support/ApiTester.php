<?php

namespace custom_pages;

use humhub\modules\content\models\ContentContainer;
use humhub\modules\custom_pages\helpers\PageType;
use humhub\modules\custom_pages\helpers\RestDefinitions;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\types\HtmlType;
use humhub\modules\user\models\User;
use Yii;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
*/
class ApiTester extends \ApiTester
{
    use _generated\ApiTesterActions;

    /**
     * Define custom actions here
     */

    /**
     * Directly persists a sample CustomPage.
     *
     * Unlike wiki/mail/tasks etc., the Custom Pages REST API is read-only (no POST endpoint), so
     * test fixtures cannot be created through the API itself. Instead this saves the CustomPage
     * (and its underlying Content record) the same way the module's own unit tests do - directly
     * through the ActiveRecord, as the given author - which is the closest equivalent to wiki's
     * ApiTester::createWikiPage() for this module.
     *
     * @param string $title
     * @param string $content
     * @param array $params Possible keys: 'containerId' (null = global page), 'type', 'target',
     * 'visibility', 'sortOrder', 'hideMenu', 'author' (username used as the page's creator)
     * @return CustomPage
     */
    public function createCustomPage($title, $content, $params = [])
    {
        $params = array_merge([
            'containerId' => null,
            'type' => HtmlType::ID,
            'target' => null,
            'visibility' => CustomPage::VISIBILITY_PUBLIC,
            'sortOrder' => null,
            'hideMenu' => false,
            'author' => 'Admin',
        ], $params);

        $this->amGoingTo('create a sample custom page');

        $container = null;
        if ($params['containerId'] !== null) {
            $contentContainer = ContentContainer::findOne(['id' => $params['containerId']]);
            $container = $contentContainer !== null ? $contentContainer->getPolymorphicRelation() : null;
        }

        $target = $params['target'] ?? ($container !== null ? PageType::TARGET_SPACE_MENU : PageType::TARGET_TOP_MENU);

        $attributes = [
            'title' => $title,
            'type' => $params['type'],
            'page_content' => $content,
            'target' => $target,
            'visibility' => $params['visibility'],
            'hide_menu' => $params['hideMenu'],
        ];

        // ContentActiveRecord::__construct($contentContainer = [], $visibility = null, $config = [])
        // only applies $attributes when $contentContainer is either an array (treated directly as
        // $config) or a real ContentContainerActiveRecord (where $attributes lands in the
        // $visibility slot and gets promoted to $config because it's an array). Passing an
        // explicit `null` container hits neither branch and silently discards $attributes, so the
        // global (no container) case must use the single-argument form instead.
        $page = $container !== null
            ? new CustomPage($container, $attributes)
            : new CustomPage($attributes);

        if ($params['sortOrder'] !== null) {
            $page->sort_order = $params['sortOrder'];
        }

        $previousIdentity = Yii::$app->user->identity;
        Yii::$app->user->switchIdentity(User::findOne(['username' => $params['author']]));

        try {
            if (!$page->save()) {
                throw new \RuntimeException('Could not save sample custom page: ' . json_encode($page->getErrors()));
            }
        } finally {
            Yii::$app->user->switchIdentity($previousIdentity);
        }

        return $page;
    }

    public function getCustomPageDefinitionById($customPageId)
    {
        $customPage = CustomPage::findOne(['id' => $customPageId]);
        return ($customPage ? RestDefinitions::getCustomPage($customPage) : []);
    }

    public function seeCustomPageDefinitionById($customPageId)
    {
        $this->seeSuccessResponseContainsJson($this->getCustomPageDefinitionById($customPageId));
    }

    /**
     * @param string $url
     * @param array $customPageIds IDs of the pages expected in the visible `results` array
     * @param array $paginationParams Possible keys: 'total', 'page', 'pages' - override when the
     * server-side total/pages differ from the visible results, e.g. when some pages returned by the
     * underlying DB query get filtered out afterwards because the current user is not allowed to
     * view them (see CustomPageController::returnFilteredPagination()).
     */
    public function seePaginationCustomPagesResponse($url, $customPageIds, $paginationParams = [])
    {
        $customPageDefinitions = [];
        foreach ($customPageIds as $customPageId) {
            $customPageDefinitions[] = $this->getCustomPageDefinitionById($customPageId);
        }

        $this->seePaginationGetResponse($url, $customPageDefinitions, $paginationParams);
    }
}
