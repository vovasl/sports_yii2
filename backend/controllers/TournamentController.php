<?php

namespace backend\controllers;


use backend\models\TournamentEventSearch;
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
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(int $id): string
    {
        $this->findModel($id);

        $model = Tournament::find()
            ->with([
                'events', 'events.totalsOver', 'events.totalsUnder'
            ])
            ->where(['id' => $id])
            ->one()
        ;
        return $this->render('view', [
            'model' => $model,
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
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionEvent(int $id): string
    {

        $searchModel = new TournamentEventSearch();
        $dataProvider = $searchModel->search($this->request->queryParams, $id);

        return $this->render('event', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $this->findModel($id),
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
