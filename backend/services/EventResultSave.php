<?php

namespace backend\services;


use backend\components\pinnacle\helpers\BaseHelper;
use frontend\models\sport\Event;
use yii\base\Component;

class EventResultSave extends Component
{

    /**
     * @param $result
     * @return array|false
     */
    public function prepare($result)
    {
        $pattern = '#(.+)\((.+?)\)#';
        preg_match_all($pattern, $result, $matches);

        if(!is_array($matches) || empty($matches[1]) || empty($matches[2])) {
            // ::log $result wrong format
            return false;
        }

        $data = [
            'sets' => explode(':', $matches[1][0]),
            'games' => explode(',', $matches[2][0])
        ];

        $data['games'] = array_combine(range(1, count($data['games'])), $data['games']);
        return $data;
    }

    public function run($id, $result, $manual = 0)
    {
        if($manual) $result = $this->prepare($result);

        if(!is_array($result)) {
            // ::log $result should be an array
            return false;
        }

        if(!$event = Event::findOne($id)) {
            // ::log event with $id don't find
            return false;
        }

        $event->home_result = $result['sets'][0];
        $event->away_result = $result['sets'][1];
        $event->winner = $result['sets'][0] > $result['sets'][1] ? $event->home : $event->away;
        $event->total = $result['sets'][0] + $result['sets'][1];

        return $event;
    }
}