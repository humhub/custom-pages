<?php


namespace humhub\modules\custom_pages\widgets;


use humhub\components\Widget;
use humhub\modules\custom_pages\interfaces\CustomPagesService;
use humhub\modules\custom_pages\models\PageType;
use humhub\modules\custom_pages\models\Target;
use yii\data\ActiveDataProvider;

class TargetPageList extends Widget
{
    /**
     * @var Target
     */
    public $target;

    /**
     * @var string
     */
    public $pageType;

    /**
     * @var CustomPagesService
     */
    private $customPagesService;

    public function init()
    {
        $this->customPagesService = new CustomPagesService();
        parent::init();
    }

    /**
     * @inheritdoc
     * @throws \yii\base\Exception
     */
    public function run()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => $this->customPagesService->findContentByTarget($this->target->id, $this->pageType, $this->target->container),
            'pagination' => [
                'pageSize' => 5
            ]
        ]);

        return $this->render('targetPageList', ['target' => $this->target, 'dataProvider' => $dataProvider, 'pageType' => $this->pageType]);
    }

}