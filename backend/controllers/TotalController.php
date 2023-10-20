<?php


namespace backend\controllers;


use backend\helpers\total\OverHelper;
use backend\models\statistic\FilterModel;
use common\helpers\EventFilterHelper;
use yii\web\Controller;

class TotalController extends Controller
{

    /**
     * @return string
     */
    public function actionEventsOver(): string
    {
        $config = [
            'futures' => 1
        ];

        $strategies = [
            OverHelper::ATPHard(),
            OverHelper::ATPIndoor(),
            //OverHelper::challengerClay(),
            //OverHelper::challengerHard(),
            //OverHelper::challengerIndoor(),
        ];

        return $this->render('events-over', [
            'strategies' => $strategies,
            'config' => $config,
        ]);
    }

}