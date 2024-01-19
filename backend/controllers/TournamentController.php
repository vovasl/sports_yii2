<?php

namespace backend\controllers;


use backend\models\statistic\total\StatisticSearch;
use frontend\models\sport\Odd;
use frontend\models\sport\Round;
use frontend\models\sport\Tournament;
use backend\models\TournamentSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * TournamentController implements the CRUD actions for Tournament model.
 */
class TournamentController extends Controller
{

    /**
     * @inheritDoc
     */
    public function behaviors(): array
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Tournament models.
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new TournamentSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Tournament model.
     * @param int $id ID
     * @return string
     */
    public function actionView(int $id): string
    {

        /** get tournament */
        $model = Tournament::find()
            ->with([
                'events', 'events.totalsOver', 'events.totalsUnder'
            ])
            ->where(['id' => $id])
            ->one()
        ;

        /** total statistic */
        $totalParams = $this->request->queryParams;
        /** default search params */
        $totalParams['StatisticSearch']['tournament_id'] = $model->id;
        $totalParams['StatisticSearch']['tournament'] = $model->name;
        $totalParams['StatisticSearch']['add_type'] = ($totalParams['StatisticSearch']['add_type']) ?? Odd::ADD_TYPE['over'];
        $totalParams['StatisticSearch']['min_moneyline'] = ($totalParams['StatisticSearch']['min_moneyline']) ?? '1.5>=';
        $totalParams['StatisticSearch']['round'] = ($totalParams['StatisticSearch']['round']) ?? Round::MAIN;
        $totalSearchModel = new StatisticSearch();
        $totalDataProvider = $totalSearchModel->search($totalParams);

        return $this->render('view', [
            'model' => $model,
            'totalSearchModel' => $totalSearchModel,
            'totalDataProvider' => $totalDataProvider,
        ]);
    }

    /**
     * Creates a new Tournament model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new Tournament();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Tournament model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate(int $id)
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
     * Finds the Tournament model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Tournament the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): Tournament
    {
        if (($model = Tournament::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested tournament does not exist.');
    }

}
