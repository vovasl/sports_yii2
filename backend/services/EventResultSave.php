<?php

namespace backend\services;


use backend\components\pinnacle\helpers\BaseHelper;
use backend\components\sofascore\models\TennisEvent;
use frontend\models\sport\Event;
use frontend\models\sport\Odd;
use frontend\models\sport\Player;
use frontend\models\sport\ResultSet;
use yii\base\Component;
use yii\db\ActiveRecord;
use yii\db\Expression;

class EventResultSave extends Component
{

    CONST LOSS = -100;

    /**
     * @param array $events
     * @param int $output
     * @return string
     */
    public function events(array $events, int $output = 0): string
    {
        $msg = "";
        foreach ($events as $event) {

            /** event with result */
            if($this->checkEventResult($event)) continue;

            $msg .= "<hr>";
            $msg .= TennisEvent::output($event);
            $msg .= "<br> Status: ";

            /** check players */
            if(!$this->checkPlayers($event)) {
                $msg .= "<span style='color: red;'>Add player sofa id</span>";
                continue;
            }

            /** check event exist */
            if(!$eventDB = $this->checkEventExist($event)) {
                $msg .= "<span style='color: red;'>Event has not been added</span>";
                continue;
            }

            /** event has not odds */
            if(count($eventDB->odds) == 0) {
                $msg .= "<span style='color: red;'>Event without odds</span>";
                continue;
            }

            $msg .= "OK";
            $msg .= "<br> Event ID: {$eventDB->id}";

            /** save event result */
            $this->run($eventDB->id, $event['result']);

            /** save event sofascore id */
            $eventDB->sofa_id = $event['id'];
            $eventDB->save();

        }
        return ($output) ? $msg : '';
    }

    /**
     * @param $id
     * @param $result
     * @param int $manual
     * @return bool|false
     */
    public function run($id, $result, int $manual = 0): bool
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

        return true;
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

    /**
     * @param array $event
     * @return false|ActiveRecord|null
     */
    private function checkEventResult(array $event)
    {
        if(empty($event['id'])) return false;
        return Event::find()
            ->where(['sofa_id' => $event['id']])
            ->andWhere(['IS NOT', 'home_result', NULL])
            ->andWhere(['IS NOT', 'away_result', NULL])
            ->one()
        ;
    }

    /**
     * @param $data
     * @return false|ActiveRecord|null
     */
    private function checkEventExist($data)
    {
        if($event = Event::find()
            ->select([Event::tableName() . '.id'])
            ->joinWith([
                'homePlayer' => function($q) {
                    $q->from(Player::tableName() . ' home');
                },
                'awayPlayer' => function($q) {
                    $q->from(Player::tableName() . ' away');
                }
            ], 0)
            ->where([
                'home.sofa_id' => $data['homeTeam']['id'],
                'away.sofa_id' => $data['awayTeam']['id']
            ])
            ->one()
        ) return $event;

        //$this->addEvent($data);

        return false;
    }

    /**
     * @param array $data
     * @return bool
     */
    private function addEvent(array $data): bool
    {

        $event = new Event();
        $event->start_at = $data['startTimestamp'];
        //$event->tournament = $data['tournament'];
        //$event->round = $event['round'];
        $event->home = Player::find()->select('id')->where(['sofa_id' => $data['homeTeam']['id']])->scalar();
        $event->away = Player::find()->select('id')->where(['sofa_id' => $data['awayTeam']['id']])->scalar();
        $event->sofa_id = $data['id'];
        $event->has_odd = 0;

        BaseHelper::outputArray($data);
        var_dump($event);
        die;

        return $event->save(0);
    }

    /**
     * @param $event
     * @return bool
     */
    private function checkPlayers($event): bool
    {
        $fields = ['homeTeam', 'awayTeam'];
        foreach ($fields as $field) {

            /** player with sofa id */
            if(Player::findBySofa($event[$field]['id'])) continue;

            /** get player by name */
            $name = explode(' ', trim($event[$field]['name']));
            $q = Player::find()
                ->where(["like", "name", "{$name[0]}"])
                ->andWhere(["IS", "sofa_id", new Expression('null')])
            ;

            /** player not found or more than one result */
            if($q->count() != 1) return false;

            /** save sofa id */
            $player = $q->one();
            $player->sofa_id = $event[$field]['id'];
            $player->save(0);
        }

        return true;
    }

}