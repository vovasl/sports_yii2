<?php

namespace backend\controllers;


use backend\components\pinnacle\helpers\BaseHelper;
use backend\components\pinnacle\helpers\OddHelper;
use backend\components\ps3838\PS3838;
use backend\models\AddLineForm;
use backend\models\AddLineLogForm;
use backend\models\AddResultForm;
use backend\models\EventSearch;
use frontend\models\sport\Event;
use frontend\models\sport\EventLog;
use frontend\models\sport\Odd;
use frontend\models\sport\ResultSet;
use Throwable;
use Yii;
use yii\db\StaleObjectException;
use yii\helpers\Json;
use yii\web\Controller;
use yii\filters\AccessControl;
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
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException|StaleObjectException|Throwable
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
    public function actionAddOdds(): string
    {
        $settings = [
            'sportId' => PS3838::TENNIS,
            'tour' => PS3838::TOUR
        ];
        $events = \Yii::$app->ps3838->run($settings);
        return $this->render('add', [
            'output' => Yii::$app->event_save->events($events)
        ]);
    }

    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionAddLine(): string
    {
        $model = new AddLineForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            /** add odds */
            if(!Odd::add($model)) {
                Yii::$app->session->setFlash('error', 'Error');
                return $this->render('add-line', ['model' => $model]);
            }

            /** get event */
            $event = $this->findModel($model->event_id);

            /** remove event result */
            if(!is_null($event->sofa_id)) {
                ResultSet::deleteAll(['event' => $event->id]);
                $event->home_result = null;
                $event->away_result = null;
                $event->winner = null;
                $event->total = null;
                $event->total_games = null;
                $event->sofa_id = null;
            }

            /** close event */
            if($model->close == 1) {
                $event->pin_id = $model::PIN_ID;
                $model = new AddLineForm();
            }
            $event->save();

            /** prepare new form */
            $model->prepare();

            Yii::$app->session->setFlash('success', 'Line has been added');
        }
        return $this->render('add-line', ['model' => $model]);
    }

    /**
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionPlayers(): array
    {
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model = $this->findModel($request->post('id'));
            return $model->dropdownPlayers();
            //return array_merge($model->dropdownPlayers(), ['' => 'Select Player']);
        }
        return [];
    }

    /**
     * @param null $id
     */
    public function actionAddLineLog($id)
    {

        /** get odds from logs */
        $eventLog = EventLog::find()->where(['event_id' => $id])->orderBy(['id' => SORT_DESC])->one();
        $log = Json::decode($eventLog->message);
        $odds = OddHelper::prepareLine($log['odds']);

        /** get event */
        $event = Event::findOne($log['id']);

        /** save odds */
        $save = (count($event->odds) < 1);
        $form = new AddLineLogForm();
        if($form->load(Yii::$app->request->post()) && $form->validate() && $save) {
            Yii::$app->event_save->addOdds([
                'id' => $log['id'],
                'home' => $log['home'],
                'away' => $log['away'],
                'odds' => $odds,
            ]);
            Yii::$app->session->setFlash('success', 'Line has been added');
            return $this->redirect(['view', 'id' => $event->id]);
        }

        return $this->render('add-line-log', [
            'event' => $event,
            'odds' => $odds,
            'log' => $log,
            'save' => $save
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
                Yii::$app->result_save->run($model, 1);

                $model = new AddResultForm();
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