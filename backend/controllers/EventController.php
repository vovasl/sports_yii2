<?php

namespace backend\controllers;


use Yii;
use backend\components\pinnacle\Pinnacle;
use yii\web\Controller;

class EventController extends Controller
{

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