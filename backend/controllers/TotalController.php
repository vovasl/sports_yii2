<?php

namespace backend\controllers;


use backend\models\statistic\total\EventTotalSearch;
use backend\models\statistic\total\PlayerTotalSearch;
use backend\models\statistic\StatisticSearch;
use common\helpers\total\PlayerHelper;
use frontend\models\sport\Odd;
use frontend\models\sport\PlayerTotal;
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
        $searchModel = new StatisticSearch();
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
    public function actionEventsOver(): string
    {
        $params = $this->request->queryParams;
        $params['EventTotalSearch']['ids'] = PlayerHelper::getEvents();
        $searchModel = new EventTotalSearch();
        $dataProvider = $searchModel->search($params);

        return $this->render('events-over', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string
     */
    public function actionEventsUnder(): string
    {
        $params = $this->request->queryParams;
        $params['EventTotalSearch']['ids'] = PlayerHelper::getEvents(Odd::ADD_TYPE['under']);
        $searchModel = new EventTotalSearch();
        $dataProvider = $searchModel->search($params);

        return $this->render('events-under', [
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
                    $model->save(0);
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