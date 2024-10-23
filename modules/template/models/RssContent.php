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

/**
 * Class RssContent
 *
 * @property string $url
 */
class RssContent extends TemplateContentActiveRecord implements TemplateContentIterable
{
    public static $label = 'RSS';

    private SimpleXMLElement|null|false $rssData = null;

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
        $result = parent::rules();
        $result[] = ['url', 'string', 'length' => [1, 1000]];
        $result[] = ['url', 'url'];
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE][] = 'url';
        $scenarios[self::SCENARIO_EDIT_ADMIN][] = 'url';
        $scenarios[self::SCENARIO_EDIT][] = 'url';
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'url' => Yii::t('CustomPagesModule.template', 'RSS feed URL'),
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
            return Html::encode(file_get_contents($this->url));
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

    /**
     * @return SimpleXMLElement|null|false
     */
    private function getRssData()
    {
        if ($this->rssData === null && !$this->isEmpty()) {
            try {
                // TODO: Cache it?
                $this->rssData = simplexml_load_file($this->url);
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
            foreach ($this->getRssData()->channel->item as $item) {
                $items[] = (array)$item;
            }
        }

        yield from $items;
    }

}
