<?php

namespace backend\controllers;


use backend\models\total\EventTotalOverSearch;
use backend\models\total\EventTotalSearch;
use backend\models\total\PlayerTotalSearch;
use backend\models\total\StatisticTotalSearch;
use frontend\models\sport\PlayerTotal;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

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
        $searchModel = new StatisticTotalSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('statistic', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
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

    /**
     * @return string
     */
    public function actionEventsTotalOver(): string
    {
        $searchModel = new EventTotalOverSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('events-total-over', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return bool
     */
    public function actionPlayerTotalAction(): bool
    {
        $request = \Yii::$app->request;
        if($request->isAjax && !empty($request->post('total'))) {
            $data = $request->post('total');
            switch ($request->post('action')) {
                case PlayerTotal::ACTION['add']:
                    /** save model */
                    $model = new PlayerTotal();
                    $model->player_id = $data['player_id'];
                    $model->tour_id = $data['tour_id'];
                    $model->surface_id = $data['surface_id'];
                    $model->type = $data['type'];
                    $model->save();
                    break;
                case PlayerTotal::ACTION['remove']:
                    /** remove model */
                    PlayerTotal::deleteAll($data);
                    break;
                default:
                    return false;
            }
            return true;
        }
        return false;
    }

}