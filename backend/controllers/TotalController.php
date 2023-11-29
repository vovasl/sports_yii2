<?php


namespace backend\controllers;

use backend\helpers\total\OverHelper;
use backend\models\total\EventTotalSearch;
use backend\models\total\PlayerTotalSearch;
use common\helpers\EventFilterHelper;
use frontend\models\sport\Odd;
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
    public function actionEvents(): string
    {
        $searchModel = new EventTotalSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('events', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string
     */
    public function actionEventsOver(): string
    {
        $config = [
            'status' => EventFilterHelper::EVENTS_STATUS['SCHEDULED'],
            'add_type' => Odd::ADD_TYPE['over'],
            'sort' => ['start_at' => SORT_ASC]
        ];

        $strategies = [
            OverHelper::ATPHard(),
            OverHelper::ATPIndoor(),
            //OverHelper::challengerClay(),
            //OverHelper::challengerHard(),
            //OverHelper::challengerIndoor(),
        ];

        return $this->render('events-over', [
            'strategies' => $strategies,
            'config' => $config,
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

}