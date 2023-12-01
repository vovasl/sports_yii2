<?php

namespace backend\controllers;


use backend\models\statistic\FilterModel;
use backend\models\total\EventTotalSearch;
use backend\models\total\PlayerTotalSearch;
use common\helpers\OddHelper;
use frontend\models\sport\Event;
use yii\filters\AccessControl;
use yii\web\Controller;

class TotalController extends Controller
{

    /**
     * @return array[]
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionStatistic(): string
    {
        $filter = new FilterModel(\Yii::$app->request->post());

        $events = Event::find();
        $events->select(['tn_event.*', 'sp_odd.id o_id', 'sp_odd.add_type o_add_type', 'sp_odd.profit o_profit', 'sp_odd.odd o_odd']);
        $events->withData();
        $events->joinWith(['odds' => function($q) {
            $q->andOnCondition(['type' => 2]);
            $q->andOnCondition(['IS NOT', 'profit', NULL]);
            return $q;
        }]);
        $events->indexBy('o_id');

        $events = $filter->searchEvents($events);
        $stats = OddHelper::eventsStats($events->all());

        //BaseHelper::outputArray($stats);die;

        return $this->render('statistic', [
            'stats' => $stats,
            'filter' => $filter,
        ]);
    }

    /**
     * @return string
     */
    public function actionPlayers(): string
    {
        $searchModel = new PlayerTotalSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('players', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string
     */
    public function actionEvents(): string
    {
        $searchModel = new EventTotalSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('events', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

}