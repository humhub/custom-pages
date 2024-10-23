<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\models;

use humhub\libs\Html;
use humhub\modules\custom_pages\modules\template\widgets\TemplateContentFormFields;
use SimpleXMLElement;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class RssContent
 *
 * @property string $url
 * @property int $cache_time
 * @property int $limit
 */
class RssContent extends TemplateContentActiveRecord implements TemplateContentIterable
{
    public static $label = 'RSS';

    private SimpleXMLElement|null|false $rssData = null;

    /**
     * @inheridoc
     */
    public function init()
    {
        parent::init();

        if ($this->cache_time === null) {
            $this->cache_time = 3600;
        }

        if ($this->limit === null) {
            $this->limit = 10;
        }
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'custom_pages_template_rss_content';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['url'], 'string', 'length' => [1, 1000]],
            [['url'], 'url'],
            [['cache_time'], 'integer', 'min' => 0],
            [['limit'], 'integer', 'min' => 0],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return ArrayHelper::merge(parent::scenarios(), [
            self::SCENARIO_CREATE => $attributes = ['url', 'cache_time', 'limit'],
            self::SCENARIO_EDIT_ADMIN => $attributes,
            self::SCENARIO_EDIT => $attributes,
        ]);
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
    public function getLabel()
    {
        return self::$label;
    }

    /**
     * @inheritdoc
     */
    public function copy()
    {
        $clone = new RssContent();
        $clone->url = $this->url;
        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function render($options = [])
    {
        try {
            return Html::encode($this->getRssData()->channel->title ?: $this->url);
        } catch (\Exception $e) {
            Yii::error('Cannot load RSS feed "' . $this->url . '". Error: ' . $e->getMessage(), 'custom-pages');
        }

        return '';
    }

    /**
     * @inheritdoc
     */
    public function isEmpty(): bool
    {
        return empty($this->url);
    }

    /**
     * @inheritdoc
     */
    public function renderEmpty($options = [])
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function renderForm($form)
    {
        return TemplateContentFormFields::widget([
            'type' => 'rss',
            'form' => $form,
            'model' => $this,
        ]);
    }

    private function getRssFileContent(): string
    {
        if ($this->isEmpty()) {
            return '';
        }

        return Yii::$app->cache->getOrSet(sha1(static::class . $this->url), function () {
            return file_get_contents($this->url);
        });
    }

    /**
     * @return SimpleXMLElement|null|false
     */
    private function getRssData()
    {
        if ($this->rssData === null && !$this->isEmpty()) {
            try {
                $this->rssData = simplexml_load_string($this->getRssFileContent());
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
                $items[] = (array) $item;
                $i++;
                if ($this->limit > 0 && $i >= $this->limit) {
                    break;
                }
            }
        }

        yield from $items;
    }

}
