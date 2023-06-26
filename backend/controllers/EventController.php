<?php

namespace backend\controllers;


use backend\models\AddResultForm;
use backend\models\EventSearch;
use frontend\models\sport\Event;
use frontend\models\sport\EventLog;
use Yii;
use yii\db\StaleObjectException;
use yii\helpers\Json;
use yii\web\Controller;
use yii\filters\AccessControl;
use backend\components\pinnacle\Pinnacle;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class EventController extends Controller
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
    public function actionIndex(): string
    {

        $searchModel = new EventSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

    }

    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        $event = Event::find()
            ->from(['event' => 'tn_event'])
            ->withData()
            ->where(['event.id' => $id])
            ->one()
        ;
        if (!$event) {
            throw new NotFoundHttpException('This event does not exist');
        }
        return $this->render('view', [
            'event' => $event
        ]);
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($id): Response
    {
        $model = $this->findModel($id);
        $model->delete();
        return $this->redirect(['index']);
    }

    /**
     * @return string
     */
    public function actionAdd(): string
    {
        $settings = [
            'sportid' => Pinnacle::TENNIS,
            'tour' => Pinnacle::ATP
        ];
        $events = Yii::$app->pinnacle->run($settings);
        return $this->render('add', [
            'output' => Yii::$app->event_save->events($events)
        ]);
    }


    /**
     * @param null $id
     * @return string
     */
    public function actionAddLine($id = null): string
    {
        $eventId = 1957;
        $save = 0;

        $id = (empty($id)) ? $eventId : $id;
        $log = EventLog::find()->where(['event_id' => $id])->orderBy(['id' => SORT_DESC])->one();
        $eventLog = Json::decode($log->message);

        $sets = $eventLog['odds']['sets'][0];
        $games = $eventLog['odds']['games'][1];

        $moneyline = [
            'moneyline' => $sets['moneyline']
        ];

        $setsSpreads = [
            'spreads' => $sets['spreads']
        ];

        $setsTotals = [
            'totals' => $sets['totals']
        ];

        $spreads = [
            'spreads' => $games['spreads']
        ];

        $totals = [
            'totals' => $games['totals']
        ];

        $teamTotals = [
            'teamTotal' => $games['teamTotal']
        ];

        $odds = [
            'sets' => array_merge($moneyline),
            //'games' => array_merge($spreads),
        ];

        $event = [
            'id' => $eventLog['id'],
            'home' => $eventLog['home'],
            'away' => $eventLog['away'],
            'odds' => $odds,
        ];

        if($save) Yii::$app->event_save->addOdds($event);

        return $this->render('add-line', [
            'log' => $eventLog,
            'event' => $event,
        ]);
    }

    /**
     * @return string
     */
    public function actionAddResult(): string
    {
        $model = new AddResultForm();
        if ($model->load(Yii::$app->request->post())) {
            if($model->validate()) {
                Yii::$app->result_save->run($model->id, $model->result, 1);

                $model->result = '';
                Yii::$app->session->setFlash('success', 'Result has been added');
            }
        }
        return $this->render('add-result', ['model' => $model]);
    }

    /**
     * @param null $data
     * @return string
     */
    public function actionAddResults($data = null): string
    {
        //$data = '2023-06-01';
        $events = Yii::$app->sofascore->getTennis($data);

        return $this->render('add-results', [
            'output' => Yii::$app->result_save->events($events, 1)
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id): Event
    {
        if (($model = Event::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested event does not exist.');
    }

}