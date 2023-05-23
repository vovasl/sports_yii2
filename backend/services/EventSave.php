<?php

namespace backend\services;


use backend\components\pinnacle\helpers\BaseHelper;
use frontend\models\sport\Event;
use frontend\models\sport\Odd;
use frontend\models\sport\OddType;
use frontend\models\sport\Player;
use frontend\models\sport\Round;
use frontend\models\sport\Sport;
use frontend\models\sport\Tour;
use frontend\models\sport\Tournament;
use yii\base\Component;
use yii\helpers\ArrayHelper;

class EventSave extends Component
{

    CONST TENNIS = 33;
    const TENNIS_ODDS_CONFIG = [
        'sets' => ['moneyline', 'spreads', 'totals'],
        'games' => ['spreads', 'totals', 'teamTotal'],
    ];
    CONST TENNIS_FIELDS_REQUIRED = ['tour', 'tournament', 'round', 'home', 'away'];

    /**
     * @param $events
     * @return string
     */
    public function events($events): string
    {
        $output = "";
        foreach ($events as $event) {

            if(empty($event['id'])) {
                // ::log empty id field
                continue;
            }

            $output .= "{$event['tournament']} {$event['round']} <br>";
            $output .= "{$event['o_starts']} {$event['home']} - {$event['away']} - ";
            $output .= $this->event($event) ? 'OK' : 'Error';
            $output .= "<br>";
        }

        return $output;
    }

    /**
     * @param array $event
     * @return bool
     */
    public function event(array $event): bool
    {

        if(empty($event['id'] || empty($event['sportid']))) {
            // ::log empty id or sportid field
            return false;
        }

        /** check sport id */
        if(!Sport::findOne($event['sportid'])) {
            // ::log add sport with id $event['sportid'] to table sp_sport
            return false;
        }

        switch ($event['sportid']) {
            case self::TENNIS:
                if(!$this->eventTennis($event)) return false;
                break;
        }

        return true;

    }

    /**
     * @param $event
     * @return bool
     */
    private function eventTennis($event): bool
    {
        /** check required fields */
        foreach (self::TENNIS_FIELDS_REQUIRED as $field) {
            if(empty($event[$field])) {
                // ::log empty required field $field
                return false;
            }
            $event[$field] = trim($event[$field]);
        }

        /** tour */
        $tour = ($tour = Tour::findOne(['name' => $event['tour']])) ? $tour : new Tour();
        if ($tour->isNewRecord) {
            $tour->name = $event['tour'];
            $tour->save();
        }
        $event['tour'] = $tour->id;

        /** tournament */
        $tournament = ($tournament = Tournament::findOne(['name' => $event['tournament'], 'tour' => $event['tour']])) ? $tournament : new Tournament();
        if($tournament->isNewRecord) {
            $tournament->name = $event['tournament'];
            $tournament->tour = $event['tour'];
            $tournament->save();
        }
        $event['tournament'] = $tournament->id;

        /** round */
        $round = ($round = Round::findOne(['name' => $event['round']])) ? $round : new Round();
        if($round->isNewRecord) {
            $round->name = $event['round'];
            $round->save();
        }
        $event['round'] = $round->id;

        /** players */
        $home = ($home = Player::findOne(['name' => $event['home']])) ? $home : new Player();
        if($home->isNewRecord) {
            $home->name = $event['home'];
            $home->save();
        }
        $event['home'] = $home->id;

        $away = ($away = Player::findOne(['name' => $event['away']])) ? $away : new Player();
        if($away->isNewRecord) {
            $away->name = $event['away'];
            $away->save();
        }
        $event['away'] = $away->id;

        /** event */
        $fixture = ($fixture = Event::findOne(['pin_id' => $event['id']])) ? $fixture : new Event();
        $fixture->start_at = $event['o_starts'];
        $updateEvent = 1;
        if($fixture->isNewRecord) {
            $updateEvent = 0;
            $fixture->tournament = $event['tournament'];
            $fixture->round = $event['round'];
            $fixture->home = $event['home'];
            $fixture->away = $event['away'];
            $fixture->pin_id = $event['id'];
        }
        $fixture->save();
        $event['id'] = $fixture->id;

        //BaseHelper::outputArray($event);
        //echo count($fixture->odds) . '<br>';

        /** exit for an existing event with odds */
        if($updateEvent && count($fixture->odds) > 0) return true;

        /** odds */
        $this->addOdds($event);

        return true;
    }

    /**
     * @param $event
     * @return bool
     */
    public function addOdds($event): bool
    {
        foreach($event['odds'] as $k => $period) {
            foreach(self::TENNIS_ODDS_CONFIG[$k] as $line) {

                if(empty($period[$line]) || !is_array($period[$line])) {
                    // ::log $event['id'] $event['odds']
                    //echo $event['id'] . '<br>';
                    //BaseHelper::outputArray($event);
                    break;
                }

                /** odd type */
                $type = ($k == 'sets' && $line != 'moneyline') ? $k . ucfirst($line) : $line;
                $oddType = ($oddType = OddType::findOne(['name' => $type])) ? $oddType : new OddType();
                if($oddType->isNewRecord) {
                    $oddType->name = $type;
                    $oddType->save();
                }

                /** save odds */
                $method = "{$type}Odds";
                if(!method_exists($this, $method)) {
                    //echo $method . '<br>';
                    // ::log add method {$method}
                    continue;
                }
                $this->{$method}($event, $period[$line], $oddType->id);
            }
        }

        return true;
    }

    /**
     * @param $event
     * @param $odd
     * @param $type
     * @return bool
     */
    private function moneylineOdds($event, $odd, $type): bool
    {
        foreach ($odd as $player => $val) {
            if(!Odd::create($event['id'], $type, $val, $event[$player])) return false;
        }

        return true;
    }

    /**
     * @param $event
     * @param $odds
     * @param $type
     * @return false
     */
    private function spreadsOdds($event, $odds, $type): bool
    {
        //BaseHelper::outputArray($odds);
        foreach ($odds as $odd) {
            $values = $this->prepareSpreadOdd($odd);
            foreach ($values as $player => $val) {
                if(!Odd::create($event['id'], $type, $val['odd'], $event[$player], $val['value'])) return false;
            }
        }

        return true;
    }

    /**
     * @param $event
     * @param $odds
     * @param $type
     * @return bool
     */
    private function setsSpreadsOdds($event, $odds, $type): bool
    {
        return $this->spreadsOdds($event, $odds, $type);
    }

    /**
     * @param $event
     * @param $odds
     * @param $type
     * @return false
     */
    private function totalsOdds($event, $odds, $type): bool
    {
        //BaseHelper::outputArray($odds);
        foreach ($odds as $odd) {
            $values = $this->prepareTotalOdd($odd);
            foreach ($values as $addType => $val) {
                if(!Odd::create($event['id'], $type, $val['odd'], null, $val['value'], $addType)) return false;
            }
        }

        return true;
    }

    /**
     * @param $event
     * @param $odds
     * @param $type
     * @return bool
     */
    private function setsTotalsOdds($event, $odds, $type): bool
    {
        return $this->totalsOdds($event, $odds, $type);
    }

    /**
     * @param $event
     * @param $odds
     * @param $type
     * @return bool
     */
    private function teamTotalOdds($event, $odds, $type): bool
    {
        foreach ($odds as $player => $odd) {
            $values = $this->prepareTotalOdd($odd);
            foreach ($values as $addType => $val) {
                if(!Odd::create($event['id'], $type, $val['odd'], $event[$player], $val['value'], $addType)) return false;
            }
        }
        return true;
    }

    /**
     * @param $odd
     * @return array[]
     */
    private function prepareSpreadOdd($odd): array
    {
        return [
            'home' => [
                'value' => $odd['hdp'],
                'odd' => $odd['home']
            ],
            'away' => [
                'value' => ($odd['hdp'] == 0) ? 0 : -$odd['hdp'],
                'odd' => $odd['away']
            ]
        ];
    }

    /**
     * @param $odd
     * @return array[]
     */
    private function prepareTotalOdd($odd): array
    {
        return [
            'over' => [
                'value' => $odd['points'],
                'odd' => $odd['over']
            ],
            'under' => [
                'value' => $odd['points'],
                'odd' => $odd['under']
            ]
        ];
    }

}