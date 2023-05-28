<?php

namespace backend\services;


use frontend\models\sport\Event;
use frontend\models\sport\Odd;
use frontend\models\sport\ResultSet;
use yii\base\Component;

class EventResultSave extends Component
{

    CONST LOSS = -100;

    /**
     * @param $id
     * @param $result
     * @param int $manual
     * @return array|false
     */
    public function run($id, $result, int $manual = 0)
    {

        if(!$event = $this->getEvent($id)) return false;
        if($manual && !$result = $this->prepare($result)) return false;
        if(!$this->validate($result)) return false;

        $result = $this->aggregate($result, $event);

        /** save event result */
        $event->home_result = $result['sets'][0];
        $event->away_result = $result['sets'][1];
        $event->winner = $event->{$result['winner']};
        $event->total = $result['setsTotals'];
        $event->total_games = $result['totals'];
        $event->five_sets = ($result['sets'][0] == 3 || $result['sets'][1] == 3) ? 1 : 0;
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

        /** save odds result */
        foreach ($event->odds as $odds) {

            if(!empty($odds->profit)) continue;

            $method = "{$odds->oddType->name}Odds";
            if(!method_exists($this, $method)) {
                // ::log add method {$method}
                continue;
            }

            $this->{$method}($odds, $result);
        }

        return $result;
    }

    /**
     * @param $id
     * @return Event|false
     */
    private function getEvent($id)
    {
        if(!$event = Event::find()
            ->with('odds', 'odds.oddType')
            ->where(['id' => $id])
            ->one())
        {
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
     * @param array $data
     * @param Event $event
     * @return array
     */
    private function aggregate(array $data, Event $event): array
    {
        $data['home_id'] = $event->home;
        $data['away_id'] = $event->away;
        $data['winner'] = $data['sets'][0] > $data['sets'][1] ? 'home' : 'away';
        $data['winner_id'] = $data['winner'] == 'home' ? $event->home : $event->away;
        $data['setsTotals'] = array_sum($data['sets']);
        $data['teamTotalHome'] = array_sum(array_column($data['games'], 0));
        $data['teamTotalAway'] = array_sum(array_column($data['games'], 1));
        $data['totals'] = $data['teamTotalHome'] + $data['teamTotalAway'];
        $data['teamSpreadHome'] = $data['teamTotalAway'] - $data['teamTotalHome'];
        $data['teamSpreadAway'] = $data['teamTotalHome'] - $data['teamTotalAway'];
        $data['teamSetsSpreadHome'] = $data['sets'][1] - $data['sets'][0];
        $data['teamSetsSpreadAway'] = $data['sets'][0] - $data['sets'][1];

        return $data;
    }

    /**
     * @param Odd $odds
     * @param array $result
     * @return bool
     */
    private function moneylineOdds(Odd $odds, array $result): bool
    {
        $odds->profit = ($odds->player_id == $result['winner_id']) ? $this->calcOdds($odds->odd) : self::LOSS;
        $odds->save();

        return true;
    }

    /**
     * @param Odd $odds
     * @param array $result
     * @return bool
     */
    private function spreadsOdds(Odd $odds, array $result): bool
    {
        $val = ($odds->player_id == $result['home_id']) ? $result['teamSpreadHome'] : $result['teamSpreadAway'];
        $odds->profit = ($odds->value > $val) ? $this->calcOdds($odds->odd) : self::LOSS;
        $odds->save();

        return true;
    }

    /**
     * @param Odd $odds
     * @param array $result
     * @return bool
     */
    private function setsSpreadsOdds(Odd $odds, array $result): bool
    {
        $val = ($odds->player_id == $result['home_id']) ? $result['teamSetsSpreadHome'] : $result['teamSetsSpreadAway'];
        $odds->profit = ($odds->value > $val) ? $this->calcOdds($odds->odd) : self::LOSS;
        $odds->save();

        return true;
    }

    /**
     * @param Odd $odds
     * @param array $result
     * @return bool
     */
    private function totalsOdds(Odd $odds, array $result): bool
    {
        $odds->profit = $this->totals($odds, $result['totals']);
        $odds->save();

        return true;
    }

    /**
     * @param Odd $odds
     * @param array $result
     * @return bool
     */
    private function setsTotalsOdds(Odd $odds, array $result): bool
    {
        $odds->profit = $this->totals($odds, $result['setsTotals']);
        $odds->save();

        return true;
    }

    /**
     * @param Odd $odds
     * @param array $result
     * @return bool
     */
    private function teamTotalOdds(Odd $odds, array $result): bool
    {
        $val = ($odds->player_id == $result['home_id']) ? $result['teamTotalHome'] : $result['teamTotalAway'];
        $odds->profit = $this->totals($odds, $val);
        $odds->save();

        return true;
    }

    /**
     * @param Odd $odds
     * @param int $val
     * @return int
     */
    private function totals(Odd $odds, int $val): int
    {
        $profit = NULL;

        /** total over */
        if($odds->add_type == Odd::ADD_TYPE['over']) {
            $profit = $val > $odds->value ? $this->calcOdds($odds->odd) : self::LOSS;
        }
        /** total under */
        else if($odds->add_type == Odd::ADD_TYPE['under']) {
            $profit = $val < $odds->value ? $this->calcOdds($odds->odd) : self::LOSS;
        }
        /** total equal */
        if($odds->value == $val) $profit = 0;

        return $profit;
    }

    /**
     * @param int $odd
     * @return int
     */
    private function calcOdds(int $odd): int
    {
        return $odd - 100;
    }

}