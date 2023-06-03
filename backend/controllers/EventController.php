<?php

namespace backend\controllers;


use backend\models\AddResultForm;
use backend\services\EventResultSave;
use frontend\models\sport\Event;
use frontend\models\sport\EventLog;
use frontend\models\sport\Tournament;
use Yii;
use yii\helpers\Json;
use yii\web\Controller;
use yii\filters\AccessControl;
use backend\components\pinnacle\Pinnacle;
use backend\components\pinnacle\helpers\BaseHelper;
use yii\web\NotFoundHttpException;

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
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex(int $id): string
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
        return $this->render('index', [
            'event' => $event
        ]);
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
     * @return string
     */
    public function actionWithoutOdds(): string
    {
        $events = Event::find()
            ->select(['event.*','odds_count' => 'count(sp_odd.id)'])
            ->from(['event' => 'tn_event'])
            ->withData()
            ->joinWith('odds')
            ->groupBy('event.id')
            ->orderTournament()
            ->having(['<=', 'odds_count', 0])
            ->all()
        ;

        return $this->render('without-odds', [
            'events' => $events
        ]);
    }


    /**
     * @param null $id
     * @return string
     */
    public function actionAddLine($id = null): string
    {
        $eventId = 718;
        $save = 0;

        $id = (empty($id)) ? $eventId : $id;
        $log = EventLog::findOne(['event_id' => $id]);
        $eventLog = Json::decode($log->message);

        $moneyline = [
            'moneyline' => $eventLog['odds']['sets'][0]['moneyline']
        ];

        $setsSpreads = [
            'spreads' => $eventLog['odds']['sets'][0]['spreads']
        ];

        $spreads = [
            'spreads' => $eventLog['odds']['games'][0]['spreads']
        ];

        $totals = [
            'totals' => $eventLog['odds']['games'][0]['totals']
        ];

        $odds = [
            //'sets' => array_merge($moneyline),
            'games' => array_merge($spreads),
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

}