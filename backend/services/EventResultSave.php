<?php

namespace backend\services;


use backend\components\pinnacle\helpers\BaseHelper;
use frontend\models\sport\Event;
use yii\base\Component;

class EventResultSave extends Component
{


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

        $result = $this->aggregate($result);

        BaseHelper::outputArray($result); die;

        /*
        $event->home_result = $result['sets'][0];
        $event->away_result = $result['sets'][1];
        $event->winner = $result['sets'][0] > $result['sets'][1] ? $event->home : $event->away;
        $event->total = $result['sets'][0] + $result['sets'][1];

        return $event;
        */
    }

    /**
     * @param $result
     * @return array|false
     */
    private function prepare($result)
    {
        $pattern = '#(.+)\((.+?)\)#';
        preg_match_all($pattern, $result, $matches);

        if(!is_array($matches) || empty($matches[1]) || empty($matches[2])) {
            // ::log $result wrong format
            return false;
        }

        $data['sets'] = explode(':', $matches[1][0]);

        /** sets games */
        $set = 1;
        $setsGames = explode(',', $matches[2][0]);
        foreach ($setsGames as $setGames) {
            $data['games'][$set++] = explode(':', $setGames);
        }

        array_walk_recursive($data, function (&$item) {
            $item = (int)trim($item);
        });

        return $data;
    }

    /**
     * @param $data
     * @return array
     */
    private function aggregate($data): array
    {
        $data['setsTotals'] = array_sum($data['sets']);
        $data['teamTotalHome'] = array_sum(array_column($data['games'], 0));
        $data['teamTotalAway'] = array_sum(array_column($data['games'], 1));
        $data['totals'] = $data['teamTotalHome'] + $data['teamTotalAway'];

        return $data;
    }
}