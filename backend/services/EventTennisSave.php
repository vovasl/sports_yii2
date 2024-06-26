<?php

namespace backend\services;


use backend\components\pinnacle\helpers\BaseHelper;
use frontend\models\sport\Event;
use frontend\models\sport\EventLog;
use frontend\models\sport\Odd;
use frontend\models\sport\OddHistory;
use frontend\models\sport\OddType;
use frontend\models\sport\Player;
use frontend\models\sport\Round;
use frontend\models\sport\Tour;
use frontend\models\sport\Tournament;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class EventTennisSave
{

    CONST FIELDS_REQUIRED = [
        'tour',
        'tournament',
        'round',
        'home',
        'away'
    ];
    CONST MIN_ODDS = 20;
    CONST CONFIG = [
        'class' => Odd::class,
        'odd' => [
            'sets' => ['moneyline', 'spreads', 'totals'],
            'games' => ['spreads', 'totals', 'teamTotal'],
        ]
    ];
    CONST CONFIG_HISTORY = [
        'class' => OddHistory::class,
        'odd' => [
            'sets' => ['moneyline'],
            'games' => [],
        ],
        'tour' => [
            1, // ATP
            5, // WTA
        ],
        'disable_round' => [
            Round::QUALIFIER
        ]
    ];

    private $oddClass;

    /**
     * @param $event
     * @return bool
     */
    public function save($event): bool
    {

        /** validate */
        if(!$event = $this->validate($event)) return false;

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

        /** save odd history */
        $this->oddHistory($event);

        /** exit for an existing event with odds(number of odds must be more than MIN value) */
        if($updateEvent && count($fixture->odds) > self::MIN_ODDS) return true;

        /** remove all odds */
        $this->removeOdds($event);

        /** odds */
        $this->addOdds($event);

        /** favorite */
        $this->addFavorite($fixture);

        /** save logs */
        $log = new EventLog();
        $log->event_id = $event['id'];
        $log->message = Json::encode($event);
        $log->save();

        return true;
    }

    /**
     * @param array $event
     * @return array|false
     */
    private function validate(array $event)
    {
        /** check required fields */
        foreach (self::FIELDS_REQUIRED as $field) {
            if(empty($event[$field])) {
                // ::log empty required field $field
                return false;
            }
            $event[$field] = trim($event[$field]);
        }

        return $event;
    }

    /**
     * @param array $event
     */
    private function removeOdds(array $event)
    {
        Odd::deleteAll(['event' => $event['id']]);
    }

    /**
     * @param array $event
     * @param array $config
     * @return bool
     */
    public function addOdds(array $event, array $config = self::CONFIG): bool
    {
        /** odd save class */
        $this->oddClass = $config['class'];
        foreach($event['odds'] as $k => $period) {
            foreach($config['odd'][$k] as $line) {

                /** empty line */
                if(empty($period[$line]) || !is_array($period[$line])) break;

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
                    // ::log add method {$method}
                    continue;
                }
                $this->$method($event, $period[$line], $oddType->id);
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
            if(!$this->oddClass::create($event['id'], $type, $val, $event[$player])) return false;
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
                if(!$this->oddClass::create($event['id'], $type, $val['odd'], $event[$player], $val['value'])) return false;
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
                if(!$this->oddClass::create($event['id'], $type, $val['odd'], null, $val['value'], $addType)) return false;
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
                if(!$this->oddClass::create($event['id'], $type, $val['odd'], $event[$player], $val['value'], $addType)) return false;
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

    /**
     * @param array $event
     * @return void
     */
    private function oddHistory(array $event): void
    {
        /** check tour */
        if(!in_array($event['tour'], self::CONFIG_HISTORY['tour'])) return;

        /** check round */
        if(in_array($event['round'], self::CONFIG_HISTORY['disable_round'])) return;

        /** add odds */
        $this->addOdds($event, self::CONFIG_HISTORY);

    }

    /**
     * @param Event $event
     * @return bool
     */
    private function addFavorite(Event $event): bool
    {
        $moneyline = ArrayHelper::map(Odd::find()
            ->select(['player_id', 'odd'])
            ->where([
                'event' => $event->id,
                'type' => Odd::TYPE['moneyline']
            ])
            ->all(), 'player_id', 'odd')
        ;

        /** event without moneyline */
        if(count($moneyline) != 2) return true;

        /** get favorite */
        foreach ($moneyline as $player_id => $odd) {
            if(is_null($event->favorite) || $odd < reset($moneyline)) {
                $event->favorite = $player_id;
            }
        }

        $event->save(0);
        return true;
    }

}