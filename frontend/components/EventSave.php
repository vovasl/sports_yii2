<?php

namespace frontend\components;


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
     * @return bool
     */
    public function events($events): bool
    {
        foreach ($events as $event) {
            if(!$this->event($event)) return false;
        }

        return true;
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

        /** event exist */
        if(Event::findOne(['pin_id' => $event['id']])) return true;

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
        $fixture = new Event();
        $fixture->start_at = $event['o_starts'];
        $fixture->tournament = $event['tournament'];
        $fixture->round = $event['round'];
        $fixture->home = $event['home'];
        $fixture->away = $event['away'];
        $fixture->pin_id = $event['id'];
        $fixture->save();
        $event['id'] = $fixture->id;

        /** odds */
        foreach($event['odds'] as $k => $period) {
            foreach(self::TENNIS_ODDS_CONFIG[$k] as $line) {

                /** odd type */
                $type = ($k == 'sets' && $line != 'moneyline') ? $k . ucfirst($line) : $line;
                $oddType = ($oddType = OddType::findOne(['name' => $type])) ? $oddType : new OddType();
                if($oddType->isNewRecord) {
                    $oddType->name = $type;
                    $oddType->save();
                }

                /** odd save method */
                $method = "{$type}Odds";
                if(method_exists($this, $method)) {
                    $this->{$method}($event, $period[$line], $oddType->id);
                }
            }
        }

        return true;
    }

    private function moneylineOdds($event, $odds, $type)
    {
        foreach ($odds as $k => $val) {
            $odd = new Odd();
            $odd->event = $event['id'];
            $odd->type = $type;
            $odd->player_id = $event[$k];
            $odd->odd = \round($val, 2) * 100;
            $odd->save();
        }
        //BaseHelper::outputArray($odds);

        return true;
    }
}