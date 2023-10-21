<?php


namespace backend\controllers;


use backend\helpers\total\OverHelper;
use backend\models\statistic\FilterModel;
use common\helpers\EventFilterHelper;
use frontend\models\sport\Odd;
use yii\web\Controller;

class TotalController extends Controller
{

    /**
     * @return string
     */
    public function actionEventsOver(): string
    {
        $config = [
            'status' => EventFilterHelper::EVENTS_STATUS['SCHEDULED'],
            'add_type' => Odd::ADD_TYPE['over'],
            'sort' => ['start_at' => SORT_ASC]
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