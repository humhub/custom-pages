<?php


namespace humhub\modules\custom_pages\widgets;


use humhub\components\Widget;
use humhub\modules\custom_pages\interfaces\CustomPagesService;
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
    public $pageTypeLabel;

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
        $pages =

        $dataProvider = new ActiveDataProvider([
            'query' => $this->customPagesService->findPagesByTarget($this->target->id, $this->target->container),
            'pagination' => [
                'pageSize' => 20
            ]
        ]);

        return $this->render('targetPageList', ['target' => $this->target, 'dataProvider' => $dataProvider, 'pageTypelabel' => $this->pageTypeLabel]);
    }

}