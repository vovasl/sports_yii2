<?php

namespace backend\controllers\statistic;

use backend\models\statistic\total\EventTotalSearch;
use backend\models\statistic\total\PlayerTotalSearch;
use backend\models\statistic\total\StatisticSearch;
use common\helpers\statistic\TotalLineHelper;
use common\helpers\statistic\TotalLineOverHelper;
use common\helpers\total\PlayerHelper;
use frontend\models\sport\Odd;
use frontend\models\sport\PlayerTotal;
use Yii;
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
    public function actionStatisticLine(): string
    {
        return $this->render('statistic-line');
    }

    /**
     * @param int $favorite
     * @return string
     */
    public function actionStatisticLineOver(int $favorite = 0): string
    {

        if ($favorite) {
            $title = 'Over Favorite';
        } else {
            $title = 'Over';

        }
        $items = TotalLineOverHelper::getItems($favorite);

        return $this->render('statistic/line', [
            'title' => $title,
            'items' => $items
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

        $params = $this->request->queryParams;

        /** get statistic line params */
        if(!empty($params['statistic-line'])) {
            $statLineParams = json_decode($params['statistic-line'], 1);

            /** get event ids */
            $statLine = TotalLineHelper::getStatistic($statLineParams);
            $statLine->select(['tn_event.id id']);
            $statLine->groupBy(['tn_event.id']);

            $params['EventTotalSearch']['event_ids'] = $statLine->column();
        }

        $searchModel = new EventTotalSearch();
        $dataProvider = $searchModel->search($params);

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

        /** empty search params */
        if(empty($params)) {
            $params['EventTotalSearch']['result'] = 2;
        }

        /** get events ids */
        $params['EventTotalSearch']['event_ids'] = array_merge(PlayerHelper::getEvents(), PlayerHelper::getEvents(PlayerTotal::TYPE['over-favorite']));
        //$params['EventTotalSearch']['event_ids'] = PlayerHelper::getEvents(PlayerTotal::TYPE['over-favorite']);

        $searchModel = new EventTotalSearch();
        $dataProvider = $searchModel->search($params);

        return $this->render('events-over', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'eventIds' => $params['EventTotalSearch']['event_ids']
        ]);
    }

    /**
     * @return string
     */
    public function actionEventsUnder(): string
    {
        $params = $this->request->queryParams;
        $params['EventTotalSearch']['event_ids'] = PlayerHelper::getEvents(Odd::ADD_TYPE['under']);
        $searchModel = new EventTotalSearch();
        $dataProvider = $searchModel->search($params);

        return $this->render('events-under', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string
     */
    public function actionPlayersOver(): string
    {
        $params = $this->request->queryParams;

        /** get events ids */
        $params['PlayerTotalSearch']['event_ids'] = PlayerHelper::getEvents();

        $searchModel = new PlayerTotalSearch();
        $dataProvider = $searchModel->search($params);

        return $this->render('players-over', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return bool
     */
    public function actionPlayerTotalAction(): bool
    {
        $request = Yii::$app->request;
        if($request->isAjax && !empty($request->post('total'))) {
            $data = $request->post('total');
            switch ($request->post('action')) {
                case PlayerTotal::ACTION['add']:

                    /** model exist */
                    if(PlayerTotal::findOne($data)) return true;

                    /** save model */
                    $model = new PlayerTotal();
                    $model->player_id = $data['player_id'];
                    $model->tour_id = $data['tour_id'];
                    $model->surface_id = $data['surface_id'];
                    $model->type = $data['type'];
                    $model->favorite = $data['favorite'];
                    $model->save(0);
                    return true;
                case PlayerTotal::ACTION['remove']:
                    /** remove model */
                    PlayerTotal::deleteAll($data);
                    return true;
                default:
                    return false;
            }
        }
        return false;
    }

}