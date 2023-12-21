<?php

namespace backend\services;


use backend\components\ps3838\PS3838;
use frontend\models\sport\Sport;
use yii\base\Component;

class EventSave extends Component
{

    private $message = '';

    /**
     * @param $events
     * @return string
     */
    public function events($events): string
    {
        foreach ($events as $event) {

            if(!$this->validate($event)) continue;

            $this->message .= "{$event['tournament']} {$event['round']}";
            $this->message .= "<br>{$event['o_starts']} {$event['home']} - {$event['away']}";
            $this->message .= $this->event($event) ? "<br>OK" : "<br>Error";
            $this->message .= "<hr>";
        }

        return $this->message;
    }

    /**
     * @param array $event
     * @return bool
     */
    public function event(array $event): bool
    {
        switch ($event['sportId']) {
            case PS3838::TENNIS:
                $handler = new EventTennisSave();
                break;
            default:
                return false;
        }

        return $handler->save($event);
    }

    /**
     * @param array $event
     * @return bool
     */
    private function validate(array $event): bool
    {
        if(empty($event['id'])) {
            // ::log empty id field
            return false;
        }

        if(empty($event['sportId'])) {
            // ::log empty sportId field
            return false;
        }

        /** check sport id */
        if(!Sport::findOne($event['sportId'])) {
            // ::log add sport with id $event['sportId'] to table sp_sport
            return false;
        }

        return true;
    }

}