<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Description of UserSearch
 *
 * @author luke
 */
class TemplateSearch extends Template
{
    public function rules()
    {
        return [
            [['name'], 'safe'],
            [['type'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Template::find();

        if ($this->type != null) {
            $query->where(['type' => $this->type]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 50],
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'id',
                'name',
                'type',
            ],
        ]);
        $dataProvider->sort->defaultOrder = ['name' => SORT_ASC];

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['type' => $this->type]);
        $query->andFilterWhere([
            'OR',
            ['id' => $this->id],
            ['like', 'name', $this->name],
        ]);

        return $dataProvider;
    }

}
