<?php

namespace backend\controllers;


use backend\components\pinnacle\helpers\BaseHelper;
use backend\components\pinnacle\helpers\OddHelper;
use backend\models\AddResultForm;
use backend\models\EventSearch;
use frontend\models\sport\Event;
use frontend\models\sport\EventLog;
use Throwable;
use Yii;
use yii\base\BaseObject;
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
    public function actionAddLine(): string
    {
        return $this->render('add-line');
    }

    /**
     * @param null $id
     * @param int $save
     * @return string
     */
    public function actionAddLineLog($id, int $save = 0): string
    {
        $eventLog = EventLog::find()->where(['event_id' => $id])->orderBy(['id' => SORT_DESC])->one();
        $log = Json::decode($eventLog->message);

        $odds = OddHelper::prepareLine($log['odds']);

        if($save == 1) {
            Yii::$app->event_save->addOdds([
                'id' => $log['id'],
                'home' => $log['home'],
                'away' => $log['away'],
                'odds' => $odds,
            ]);
        }

        return $this->render('add-line-log', [
            'event' => Event::findOne($log['id']),
            'odds' => $odds,
            'log' => $log,
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