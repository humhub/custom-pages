<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2016 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\components;

use yii\base\Component;
use yii\web\UrlRuleInterface;
use humhub\modules\custom_pages\models\CustomPage;

/**
 * CustomPages URL Rule
 *
 * @author luke
 */
class PageUrlRule extends Component implements UrlRuleInterface
{
    /**
     * @var string default route to page home
     */
    public $defaultRoutes = ['custom_pages/view', 'custom_pages/view/view'];

    /**
     * @var array map with space guid/url pairs
     */
    protected static $pageUrlMap = [];

    /**
     * @inheritdoc
     */
    public function createUrl($manager, $route, $params)
    {
        // If in content container
        if (isset($params['cguid'])) {
            return false;
        }

        if (in_array($route, $this->defaultRoutes) && isset($params['id'])) {

            $route = '';

            $urlPart = static::getUrlByPageId($params['id']);
            if ($urlPart !== null) {
                $url = "p/" . urlencode($urlPart);
                unset($params['id']);

                if (!empty($params) && ($query = http_build_query($params)) !== '') {
                    $url .= '?' . $query;
                }
                return $url;
            }
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function parseRequest($manager, $request)
    {
        $pathInfo = $request->getPathInfo();
        if (str_starts_with((string) $pathInfo, 'p/')) {
            $parts = explode('/', (string) $pathInfo, 3);
            if (isset($parts[1])) {
                $page = CustomPage::find()
                    ->andWhere([
                        'OR',
                        [CustomPage::tableName() . '.id' => $parts[1]],
                        [CustomPage::tableName() . '.url' => $parts[1]],
                    ])
                    ->one();
                if ($page !== null) {
                    if (!isset($parts[2]) || $parts[2] == "") {
                        $parts[2] = $this->defaultRoutes[0];
                    }

                    $params = $request->get();
                    $params['id'] = $page->id;

                    return [$parts[2], $params];
                }
            }
        }
        return false;
    }

    /**
     * Gets space url name by given guid
     *
     * @param string $guid
     * @return string|null the space url part
     */
    public static function getUrlByPageId($id)
    {
        if (isset(static::$pageUrlMap[$id])) {
            return static::$pageUrlMap[$id];
        }

        $page = CustomPage::findOne(['id' => $id]);
        if ($page !== null) {
            static::$pageUrlMap[$page->id] = !empty($page->url) ? $page->url : $page->id;
            return static::$pageUrlMap[$page->id];
        }

        return null;
    }

}
