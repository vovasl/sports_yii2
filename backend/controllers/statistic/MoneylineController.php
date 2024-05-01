<?php

namespace backend\controllers\statistic;

use backend\models\statistic\moneyline\StatisticSearch;
use yii\web\Controller;

use yii\filters\AccessControl;

class MoneylineController extends Controller
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

}