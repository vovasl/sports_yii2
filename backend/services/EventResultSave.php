<?php

namespace backend\services;


use backend\components\pinnacle\helpers\BaseHelper;
use frontend\models\sport\Event;
use frontend\models\sport\ResultSet;
use yii\base\Component;

class EventResultSave extends Component
{

    /**
     * @param $id
     * @param $result
     * @param int $manual
     * @return false|Event
     */
    public function run($id, $result, int $manual = 0)
    {

        if(!$event = $this->getEvent($id)) return false;
        if($manual && !$result = $this->prepare($result)) return false;
        if(!$this->validate($result)) return false;

        $result = $this->aggregate($result);

        /** save event result */
        $event->home_result = $result['sets'][0];
        $event->away_result = $result['sets'][1];
        $event->winner = $event->{$result['winner']};
        $event->total = $result['setsTotals'];
        $event->total_games = $result['totals'];
        $event->save();

        /** save event sets result */
        foreach ($result['games'] as $set => $games) {
            $setRes = new ResultSet();
            $setRes->event = $event->id;
            $setRes->set = $set;
            $setRes->home = $games[0];
            $setRes->away = $games[1];
            $setRes->save();
        }

        return $event;
    }

    /**
     * @param $id
     * @return Event|false
     */
    private function getEvent($id)
    {
        if(!$event = Event::findOne($id)) {
            // ::log event with $id don't find
            return false;
        }

        if(!empty($event->home_result) || !empty($event->away_result)) {
            // ::log event with $id has result
            return false;
        }

        return $event;
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
     * @return bool
     */
    private function validate($data): bool
    {
        if(!is_array($data)) {
            // ::log $result should be an array
            return false;
        }

        if(empty($data['sets']) || empty($data['games'])) {
            // :: log print_r($data, 1) wrong format
            return false;
        }

        return true;
    }

    /**
     * @param $data
     * @return array
     */
    private function aggregate($data): array
    {
        $data['winner'] = $data['sets'][0] > $data['sets'][1] ? 'home' : 'away';
        $data['setsTotals'] = array_sum($data['sets']);
        $data['teamTotalHome'] = array_sum(array_column($data['games'], 0));
        $data['teamTotalAway'] = array_sum(array_column($data['games'], 1));
        $data['totals'] = $data['teamTotalHome'] + $data['teamTotalAway'];

        return $data;
    }

}