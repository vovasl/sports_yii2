<?php


namespace backend\controllers;

use backend\helpers\total\OverHelper;
use common\helpers\EventFilterHelper;
use frontend\models\sport\Odd;
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