<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\libs\Html;
use humhub\modules\custom_pages\modules\template\interfaces\TemplateElementContentIterable;
use SimpleXMLElement;
use Yii;

/**
 * Class to manage content records of the RSS elements
 *
 * Dynamic attributes:
 * @property string $url
 * @property int $cache_time
 * @property int $limit
 */
class RssElement extends BaseElementContent implements TemplateElementContentIterable
{
    /**
     * @inheritdoc
     */
    public function getLabel(): string
    {
        return Yii::t('CustomPagesModule.template', 'Rss');
    }

    private SimpleXMLElement|null|false $rssData = null;

    /**
     * @inheritdoc
     */
    protected function getDynamicAttributes(): array
    {
        return [
            'url' => null,
            'cache_time' => 3600,
            'limit' => 10,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['url'], 'string', 'length' => [1, 1000]],
            [['url'], 'url'],
            [['cache_time'], 'integer', 'min' => 0],
            [['limit'], 'integer', 'min' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'url' => Yii::t('CustomPagesModule.template', 'RSS feed URL'),
            'cache_time' => Yii::t('CustomPagesModule.template', 'Expire Time (in seconds)'),
            'limit' => Yii::t('CustomPagesModule.template', 'Limit items'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
            'cache_time' => Yii::t('CustomPagesModule.template', 'Leave blank to don\'t cache.'),
            'limit' => Yii::t('CustomPagesModule.template', 'Leave blank to don\'t limit.'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function render($options = [])
    {
        return Html::encode($this->getRssData()->channel->title ?: $this->url);
    }

    /**
     * @inheritdoc
     */
    public function isEmpty(): bool
    {
        return empty($this->url);
    }

    private function getRssFileContent(): string
    {
        if ($this->isEmpty()) {
            return '';
        }

        try {
            if ($this->cache_time > 0) {
                return Yii::$app->cache->getOrSet(sha1(static::class . $this->url), function () {
                    return file_get_contents($this->url);
                }, $this->cache_time);
            }

            return file_get_contents($this->url);
        } catch (\Exception $e) {
            Yii::error('Cannot load RSS feed "' . $this->url . '". Error: ' . $e->getMessage(), 'custom-pages');
            return '';
        }
    }

    /**
     * @return SimpleXMLElement|null|false
     */
    private function getRssData()
    {
        if ($this->rssData === null && !$this->isEmpty()) {
            try {
                $this->rssData = simplexml_load_string($this->getRssFileContent(), SimpleXMLElement::class, LIBXML_NOCDATA, '');
                // Register all found namespaces in the RSS feed
                foreach ($this->rssData->getNamespaces(true) as $prefix => $namespace) {
                    $this->rssData->registerXPathNamespace($prefix, $namespace);
                }
            } catch (\Exception $e) {
                $this->rssData = false;
            }
        }

        return $this->rssData;
    }

    /**
     * @inheridoc
     */
    public function getItems(): iterable
    {
        $items = [];
        if ($this->getRssData()->channel->item instanceof SimpleXMLElement) {
            $i = 0;
            foreach ($this->getRssData()->channel->item as $item) {
                $fields = (array) $item;
                $fields = $this->parseItemNamespacedFields($item, $fields);
                $fields = $this->convertSimpleXMLElementsToArray($fields);
                $fields = $this->parseItemImage($fields);

                $items[] = $fields;
                $i++;

                if ($this->limit > 0 && $i >= $this->limit) {
                    break;
                }
            }
        }

        yield from $items;
    }

    protected function parseItemNamespacedFields($item, array $fields = []): array
    {
        $namespaces = $this->getRssData()->getNamespaces(true);

        foreach ($namespaces as $prefix => $namespace) {
            $namespacedElements = $item->xpath($prefix . ':*');
            foreach ($namespacedElements as $element) {
                if (!isset($fields[$prefix])) {
                    $fields[$prefix] = (string) $element;
                }
                $fields[$prefix . '_' . $element->getName()] = (string) $element;
            }
        }

        return $fields;
    }

    protected function parseItemImage(array $fields = []): array
    {
        if (isset($fields['imageUrl'])) {
            return $fields;
        }

        if (!empty($fields['enclosure']['url'])) {
            $fields['imageUrl'] = $fields['enclosure']['url'];
            return $fields;
        }

        $imageFields = ['content', 'description'];

        foreach ($imageFields as $imageField) {
            if (isset($fields[$imageField]) &&
                preg_match('/<img.+?src="(.+?)".+?>/i', $fields[$imageField], $image)) {
                $fields['imageUrl'] = $image[1];
                break;
            }
        }

        return $fields;
    }

    protected function convertSimpleXMLElementsToArray(array $array): array
    {
        foreach ($array as $a => $element) {
            if ($element instanceof SimpleXMLElement) {
                $element = (array) $element;
                if (isset($element['@attributes'])) {
                    $element = array_merge($element, (array) $element['@attributes']);
                    unset($element['@attributes']);
                }
                $array[$a] = $element === []
                    ? '' // Fix wrong empty array when an empty string is expected from data like <description><![CDATA[]]></description>
                    : $this->convertSimpleXMLElementsToArray($element);
            }
        }

        return $array;
    }

}
