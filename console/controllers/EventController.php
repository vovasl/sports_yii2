<?php

namespace console\controllers;


use backend\components\ps3838\PS3838;
use Yii;
use yii\console\Controller;

class EventController extends Controller
{
    public function actionAdd()
    {
        $settings = [
            'sportid' => PS3838::TENNIS,
            'tour' => PS3838::ATP
        ];
        $events = \Yii::$app->ps3838->run($settings);

        $i = 0;
        $output = "";
        $count = count($events);

        //Console::output('Events');
        //Console::startProgress($i, $count, 'Status');
        foreach ($events as $event) {
            $i++;
            //Console::updateProgress($i, $count);

            $output .= "\n";
            $output .= "{$event['tournament']} {$event['round']} \n";
            $output .= "{$event['o_starts']} {$event['home']} - {$event['away']} - ";
            $output .= Yii::$app->event_save->event($event) ? 'OK' : 'Error';

        }
        //Console::output($output);
    }
}