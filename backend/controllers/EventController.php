<?php

namespace backend\controllers;


use frontend\models\sport\Round;
use Yii;
use backend\components\pinnacle\Pinnacle;
use yii\base\BaseObject;
use yii\filters\AccessControl;
use yii\web\Controller;

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

    public function actionAdd()
    {
        $settings = [
            'sportid' => Pinnacle::TENNIS,
            'tour' => Pinnacle::ATP
        ];
        $events = Yii::$app->pinnacle->run($settings);
        echo Yii::$app->event_save->events($events);
    }

}