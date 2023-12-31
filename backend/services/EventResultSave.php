<?php

namespace backend\services;


use backend\components\pinnacle\helpers\BaseHelper;
use backend\components\sofascore\models\TennisEvent;
use backend\helpers\EventResultSaveHelper;
use backend\models\AddResultForm;
use backend\models\odd\Calculate;
use frontend\models\sport\Event;
use frontend\models\sport\Odd;
use frontend\models\sport\Player;
use frontend\models\sport\PlayerAddEvent;
use frontend\models\sport\ResultSet;
use frontend\models\sport\Round;
use frontend\models\sport\Tour;
use frontend\models\sport\Tournament;
use yii\base\Component;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\StaleObjectException;

class EventResultSave extends Component
{

    CONST FINISHED_STATUSES = [100];

    private $message = '';

    /**
     * @param array $events
     * @param int $output
     * @return string
     */
    public function events(array $events, int $output = 0): string
    {
        foreach ($events as $event) {

            /** check event */
            if($this->checkEvent($event)) continue;

            $this->message .= "<hr>" . TennisEvent::output($event);

            /** check players */
            if(!$this->issetPlayers($event)) continue;

            if(empty($event['winnerCode'])) {
                $this->message .= EventResultSaveHelper::errorMsg('Empty winnerCode');
                continue;
            }

            /** get event */
            $eventDB = $this->getEventData($event);

            $this->message .= "<br>" . EventResultSaveHelper::getLink($eventDB->id);

            /** pinnacle odds has not added */
            if(!$this->issetOdds($eventDB)) continue;

            /** add result form model */
            $model = new AddResultForm();
            $model->id = $eventDB->id;
            $model->winner = $event['winnerCode'];
            $model->status = $event['status']['code'];
            $model->result = $event['result'];
            $model->sofa_id = $event['id'];

            /** save event result */
            if(!$this->run($model)) {
               //$this->message .= $event['id'];
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

        /** get result */
        if($manual && !$model->result = $this->prepareResult($model->result)) return false;
        if(!$this->validateResult($model)) return false;
        $result = $this->aggregate($model->result, $event, $model->winner);

        /** save event result */
        $event->home_result = !empty($result['sets'][0]) ? $result['sets'][0] : 0;
        $event->away_result = !empty($result['sets'][1]) ? $result['sets'][1] : 0;
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
        if(isset($result['games']) && is_array($result['games'])) {
            foreach ($result['games'] as $set => $games) {
                $setRes = new ResultSet();
                $setRes->event = $event->id;
                $setRes->set = $set;
                $setRes->home = $games[0];
                $setRes->away = $games[1];
                $setRes->save();
            }
        }

        /** calculate odds profit */
        $calculate = new Calculate($event->odds, $result);
        $calculate->run();

        $this->afterSave($event);

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
            $this->message .= EventResultSaveHelper::warningMsg('Event has result');
            return false;
        }

        return $event;
    }

    /**
     * @param $result
     * @return array|false
     */
    private function prepareResult($result)
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
     * @param AddResultForm $model
     * @return bool
     */
    private function validateResult(AddResultForm $model): bool
    {
        if(!is_array($model->result)) {
            // ::log $result should be an array
            return false;
        }

        if(in_array($model->status, self::FINISHED_STATUSES) && (empty($model->result['sets']) || empty($model->result['games']))) {
            // :: log print_r($data, 1) wrong format
            $this->message .= EventResultSaveHelper::errorMsg('Wrong result format');
            return false;
        }

        return true;
    }

    /**
     * @param array $data
     * @param Event $event
     * @param int $winner
     * @return array
     */
    private function aggregate(array $data, Event $event, int $winner): array
    {
        $data['home_id'] = $event->home;
        $data['away_id'] = $event->away;
        $data['winner'] = $this->getWinner($winner, $data);
        $data['winner_id'] = $data['winner'] == 'home' ? $event->home : $event->away;
        $data['setsTotals'] = is_array($data['sets']) ? array_sum($data['sets']) : 0;
        $data['teamTotalHome'] = (isset($data['games']) && is_array($data['games']))
            ? array_sum(array_column($data['games'], 0))
            : 0
        ;
        $data['teamTotalAway'] = (isset($data['games']) && is_array($data['games']))
            ? array_sum(array_column($data['games'], 1))
            : 0
        ;
        $data['totals'] = $data['teamTotalHome'] + $data['teamTotalAway'];
        $data['teamSpreadHome'] = $data['teamTotalAway'] - $data['teamTotalHome'];
        $data['teamSpreadAway'] = $data['teamTotalHome'] - $data['teamTotalAway'];
        $data['teamSetsSpreadHome'] = $data['sets'][1] - $data['sets'][0];
        $data['teamSetsSpreadAway'] = $data['sets'][0] - $data['sets'][1];

        return $data;
    }

    /**
     * @param int $val
     * @param array $data
     * @return string
     */
    private function getWinner(int $val, array $data): string
    {
        switch ($val) {
            case 1:
                $winner = 'home';
                break;
            case 2:
                $winner = 'away';
                break;
            default:
                $winner = '';
        }

        if(empty($winner)) {
            $winner = $data['sets'][0] > $data['sets'][1] ? 'home' : 'away';
            $this->message .= EventResultSaveHelper::warningMsg('Check out winner field');
        }

        return $winner;
    }

    /**
     * @param array $event
     * @return bool
     */
    private function checkEvent(array $event): bool
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
            $this->message .= "<br>" . EventResultSaveHelper::getEditLink($event->id);
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
            $this->message .= EventResultSaveHelper::warningMsg("Add information about the tournament. Empty [uniqueTournament]");
        }
        else if(!$val = Tournament::getIdBySofa($data['tournament']['uniqueTournament']['id'])) {
            $this->message .= EventResultSaveHelper::warningMsg("Add information about the tournament. Unable to find {$data['tournament']['uniqueTournament']['id']}");
        }

        return $val;
    }

    /**
     * @param $data
     * @return int|null
     */
    private function getRound($data): ?int
    {
        $round = (empty($data['roundInfo']['name'])) ? $data['tournament']['name'] : $data['roundInfo']['name'];
        if(!$val = Round::getIdBySofa($round, $data['tournament']['category']['id'])) {
            $this->message .= EventResultSaveHelper::warningMsg("Add information about the round to the event. Unable to find {$data['roundInfo']['name']}");
        }

        /** remove after debug */
        if(empty($data['roundInfo']['name'])) {
            $this->message .= EventResultSaveHelper::warningMsg("Check information about the round");
        }
        if(isset($data['roundInfo']['name']) && $data['roundInfo']['name'] == 'Round of 32' && $data['tournament']['category']['id'] == Tour::SOFA_CHALLENGER) {
            $this->message .= EventResultSaveHelper::warningMsg("Check information about the round");
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
                if($q->count() > 1) {
                    $message = "Add player {$event[$field]['name']} sofa id";
                }
                else {

                    /** player does not exists - event tracks  */
                    if(!PlayerAddEvent::findOne(['sofa_id' => $event['id']])) {
                        PlayerAddEvent::add($event, $field);
                    }
                    $message = "Player {$event[$field]['name']} does not exist";
                    $message .= "<br> Event Sofa Id: {$event['id']}";
                }
                $this->message .= EventResultSaveHelper::errorMsg($message);
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
            $this->message .= EventResultSaveHelper::errorMsg("Event without odds");
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
        if(!empty($event->pin_id)) $this->message .= "<br>" . EventResultSaveHelper::getEditLink($event->id);

        $event->total = null;
        $event->status = 0;
        $event->total_games = null;
        $event->pin_id = null;
        $event->save();

        /** remove odds */
        Odd::deleteAll(['event' => $event->id]);

        return $event;
    }

    /**
     * @param Event $event
     * @throws \Throwable
     * @throws StaleObjectException
     */
    private function afterSave(Event $event)
    {
        /** delete player add event */
        PlayerAddEvent::removeBySofa($event->sofa_id);

    }

}