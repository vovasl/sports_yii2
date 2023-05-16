<?php

namespace console\controllers;


use backend\components\pinnacle\Pinnacle;
use frontend\models\sport\Event;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class EventController extends Controller
{
    public function actionAdd()
    {
        $settings = [
            'sportid' => Pinnacle::TENNIS,
            'tour' => Pinnacle::ATP
        ];
        $events = Yii::$app->pinnacle->run($settings);

        $i = 0;
        $output = "";
        $count = count($events);

        Console::output('Events');
        Console::startProgress($i, $count, 'Status');
        foreach ($events as $event) {
            $i++;
            Console::updateProgress($i, $count);

            /** event exist */
            if(Event::findOne(['pin_id' => $event['id']])) continue;

            $output .= "\n";
            $output .= "{$event['tournament']} {$event['round']} \n";
            $output .= "{$event['o_starts']} {$event['home']} - {$event['away']} - ";
            $output .= Yii::$app->event_save->event($event) ? 'OK' : 'Error';

        }
        Console::output($output);
    }
}