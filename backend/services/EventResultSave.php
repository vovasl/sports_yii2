<?php

namespace backend\services;


use backend\components\pinnacle\helpers\BaseHelper;
use backend\components\sofascore\models\TennisEvent;
use backend\models\AddResultForm;
use frontend\models\sport\Event;
use frontend\models\sport\Odd;
use frontend\models\sport\Player;
use frontend\models\sport\ResultSet;
use frontend\models\sport\Round;
use frontend\models\sport\Tournament;
use yii\base\Component;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Html;

class EventResultSave extends Component
{

    CONST LOSS = -100;
    const FINISHED_STATUSES = [100];

    private $message = '';

    /**
     * @param array $events
     * @param int $output
     * @return string
     */
    public function events(array $events, int $output = 0): string
    {
        foreach ($events as $event) {

            /** event with result */
            if($this->eventHasResult($event)) continue;

            $this->message .= "<hr>" . TennisEvent::output($event);

            /** check players */
            if(!$this->issetPlayers($event)) continue;

            /** get event */
            $eventDB = $this->getEventData($event);

            $this->message .= "<br>" . self::getLink($eventDB->id);

            /** pinnacle odds has not added */
            if(!$this->issetOdds($eventDB)) continue;

            /** add result form model */
            $model = new AddResultForm();
            $model->id = $eventDB->id;
            $model->status = $event['status']['code'];
            $model->result = $event['result'];
            $model->sofa_id = $event['id'];

            /** save event result */
            if(!$this->run($model)) {
               $this->message .= $event['id'];
                continue;
            }

            $this->message .= "<br> Status: OK";

        }
        return ($output) ? $this->message : '';
    }

    /**
     * @param AddResultForm $model
     * @param int $manual
     * @return bool|false
     */
    public function run(AddResultForm $model, int $manual = 0): bool
    {
        /** @var Event $event */
        if(!$event = $this->getEvent($model->id)) return false;
        if($manual && !$model->result = $this->prepare($model->result)) return false;
        if(!$this->validate($model->result)) return false;

        $result = $this->aggregate($model->result, $event);

        /** save event result */
        $event->home_result = $result['sets'][0];
        $event->away_result = $result['sets'][1];
        $event->winner = $event->{$result['winner']};
        $event->total = $result['setsTotals'];
        $event->total_games = $result['totals'];
        $event->five_sets = ($result['sets'][0] == 3 || $result['sets'][1] == 3) ? 1 : 0;
        $event->sofa_id = $model->sofa_id;

        /** event was not finished */
        if(!in_array($model->status, self::FINISHED_STATUSES)) {
            $event = $this->eventNotFinished($event);
        }

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

        /** calculate odds profit */
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
     * @return ActiveRecord|array|false|null
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
            $this->message .= $this->warningMsg('Event has result');
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
            $this->message .= $this->errorMsg('Wrong result format');
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
     * @return bool
     */
    private function eventHasResult(array $event): bool
    {
        if(empty($event['id'])) return false;
        $event = Event::find()
            ->where(['sofa_id' => $event['id']])
            ->andWhere(['IS NOT', 'home_result', NULL])
            ->andWhere(['IS NOT', 'away_result', NULL])
            ->andWhere(['IS NOT', 'winner', NULL])
            ->one()
        ;

        return (bool)$event;
    }

    /**
     * @param array $data
     * @return ActiveRecord|null
     */
    private function getEventData(array $data)
    {
        if(!$event = Event::find()
            ->select([Event::tableName() . '.*'])
            ->with(['odds'])
            ->joinWith([
                'homePlayer' => function($q) {
                    $q->from(Player::tableName() . ' home');
                },
                'awayPlayer' => function($q) {
                    $q->from(Player::tableName() . ' away');
                }
            ], 0)
            ->where([
                'home_result' => NULL,
                'away_result' => NULL,
                'winner' => NULL,
                'home.sofa_id' => $data['homeTeam']['id'],
                'away.sofa_id' => $data['awayTeam']['id']
            ])
            ->one()
        ) {
            /** add event */
            $event = $this->addEvent($data);
        }

        return $event;
    }

    /**
     * @param array $data
     * @return Event
     */
    private function addEvent(array $data): Event
    {
        $event = new Event();
        $event->start_at = date('Y-m-d H:i:s', $data['startTimestamp']);
        $event->tournament = $this->getTournament($data);
        $event->round = $this->getRound($data);
        $event->home = Player::getIdBySofa($data['homeTeam']['id']);
        $event->away = Player::getIdBySofa($data['awayTeam']['id']);
        $event->sofa_id = $data['id'];
        $event->save(0);

        return $event;
    }

    /**
     * @param $data
     * @return int|null
     */
    private function getTournament($data): ?int
    {
        $val = null;
        if(empty($data['tournament']['uniqueTournament']['id'])) {
            $this->message .= $this->warningMsg("Add information about the tournament. Empty [uniqueTournament]");
        }
        else if(!$val = Tournament::getIdBySofa($data['tournament']['uniqueTournament']['id'])) {
            $this->message .= $this->warningMsg("Add information about the tournament. Unable to find {$data['tournament']['uniqueTournament']['id']}");
        }

        return $val;
    }

    /**
     * @param $data
     * @return int|null
     */
    private function getRound($data): ?int
    {
        $val = null;
        if(empty($data['roundInfo']['name'])) {
            $this->message .= $this->warningMsg("Add information about the round to the event. Empty [roundInfo]");

        }
        else if(!$val = Round::getIdBySofa($data['roundInfo']['name'])) {
            $this->message .= $this->warningMsg("Add information about the round to the event. Unable to find {$data['roundInfo']['name']}");
        }

        return $val;
    }

    /**
     * @param $event
     * @return bool
     */
    private function issetPlayers($event): bool
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
            if($q->count() != 1) {
                $message = ($q->count() == 0) ? "Player {$event[$field]['name']} does not exist" : "Add player {$event[$field]['name']} sofa id";
                $this->message .= $this->errorMsg($message);
                return false;
            }

            /** save sofa id */
            $player = $q->one();
            $player->sofa_id = $event[$field]['id'];
            $player->save(0);
        }

        return true;
    }

    /**
     * @param Event $event
     * @return bool
     */
    private function issetOdds(Event $event): bool
    {
        if(!empty($event->pin_id) && count($event->odds) == 0) {
            $this->message .= $this->errorMsg("Event without odds");
            return false;
        }

        return true;
    }

    /**
     * @param Event $event
     * @return Event
     */
    private function eventNotFinished(Event $event): Event
    {
        $event->total = null;
        $event->status = 0;
        $event->total_games = null;
        $event->pin_id = null;
        $event->save();

        /** remove odds */
        $this->removeOdds($event->id);

        $this->message .= $this->warningMsg('Event was not finished. Check out fields: winner, home_result, away_result, five_sets');
        $this->message .= Html::a('Edit', ['/event/edit', 'id' => $event->id], ['target'=>'_blank']);

        return $event;
    }

    /**
     * @param int $id
     */
    private function removeOdds(int $id)
    {
        Odd::deleteAll(['event' => $id]);
    }

    /**
     * @param string $message
     * @return string
     */
    private function errorMsg(string $message): string
    {
        return "<br><span style='color: red;'>{$message}</span>";
    }

    /**
     * @param string $message
     * @return string
     */
    private function warningMsg(string $message): string
    {
        return "<br><span style='color: coral;'>{$message}</span>";
    }

    /**
     * @param $id
     * @return string
     */
    public static function getLink($id): string
    {
        return Html::a('Link', ['/event/view', 'id' => $id], ['target'=>'_blank']);
    }

}