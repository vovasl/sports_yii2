<?php

namespace common\helpers\total;


use common\helpers\TotalHelper;
use frontend\models\sport\Event;
use frontend\models\sport\Odd;
use frontend\models\sport\PlayerTotal;
use frontend\models\sport\query\EventQuery;
use frontend\models\sport\Round;
use frontend\models\sport\Tour;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class PlayerHelper
{

    CONST MONEYLINE =[
        'over' => [
            'min' => 150
        ],
        'under' => [
            'max' => 140
        ],
    ];

    /**
     * @param string $type
     * @return array
     */
    public static function getEvents(string $type = Odd::ADD_TYPE['over']): array
    {
        $searchModels = PlayerTotal::find()
            ->where(['type' => $type])
            ->groupBy(['tour_id', 'surface_id'])
            ->all()
        ;

        $ids = [];
        foreach ($searchModels as $searchModel) {

            /** get tour ids */
            $tourIds = in_array($searchModel->tour_id,Tour::ATP_ALL) ? Tour::ATP_ALL : [$searchModel->tour_id];

            /** get players total models */
            $playersTotal = PlayerTotal::find()
                ->where(['IN', 'tour_id', $tourIds])
                ->andWhere(['surface_id' => $searchModel->surface_id])
                ->andWhere(['type' => $type])
                ->all()
            ;

            /** get events */
            $query = Event::find()
                ->select([
                    'tn_event.*',
                    'home_moneyline.odd home_moneyline_odd',
                    'away_moneyline.odd away_moneyline_odd'
                ])
                ->withData()
                ->joinWith([
                    'homeMoneyline',
                    'awayMoneyline',
                ])
                ->where(['<>', Round::tableName() . '.id', Round::QUALIFIER])
                ->andWhere(['IN', 'tn_tournament.tour', $tourIds])
                ->andWhere(['tn_tournament.surface' => $searchModel->surface_id])
            ;

            /** get where */
            $query = ($type == Odd::ADD_TYPE['over'])
                ? self::getWhereOver($query, ArrayHelper::getColumn($playersTotal, 'player_id'))
                : self::getWhereUnder($query, ArrayHelper::getColumn($playersTotal, 'player_id'))
            ;

            $events = ($type == Odd::ADD_TYPE['over'])
                ? ArrayHelper::getColumn($query->all(), 'id')
                : self::getEventsUnder($query->all(), $playersTotal)
            ;

            $ids = array_merge($ids, $events);
        }

        return $ids;
    }

    /**
     * @param ActiveQuery $model
     * @param array $players
     * @return ActiveQuery
     */
    public static function getWhereOver(ActiveQuery $model, array $players): ActiveQuery
    {
        $model->andWhere(['IN', 'home', $players]);
        $model->andWhere(['IN', 'away', $players]);
        $model->andWhere(['>=', 'home_moneyline.odd', self::MONEYLINE['over']['min']]);
        $model->andWhere(['>=', 'away_moneyline.odd', self::MONEYLINE['over']['min']]);

        return $model;
    }

    /**
     * @param ActiveQuery $model
     * @param array $players
     * @return ActiveQuery
     */
    public static function getWhereUnder(ActiveQuery $model, array $players): ActiveQuery
    {
        $model->andWhere(['OR',
            ['IN', 'home', $players],
            ['IN', 'away', $players]
        ]);
        $model->andWhere(['OR',
            ['<', 'home_moneyline.odd', self::MONEYLINE['under']['max']],
            ['<', 'away_moneyline.odd', self::MONEYLINE['under']['max']]
        ]);

        return $model;
    }

    /**
     * @param array $models
     * @param array $playersTotal
     * @return array
     */
    public static function getEventsUnder(array $models, array $playersTotal): array
    {
        $events = [];
        foreach ($playersTotal as $playerTotal) {
            foreach ($models as $k => $model) {

                /** check if player's event */
                if(!in_array($playerTotal->player_id, [$model->home, $model->away])) continue;

                /** get player moneyline field */
                $moneyline = ($playerTotal->player_id == $model->home) ? 'home_moneyline_odd' : 'away_moneyline_odd';

                /** check event */
                if(($playerTotal->favorite === 1 && $model->$moneyline >= self::MONEYLINE['under']['max'])
                    || ($playerTotal->favorite === 0 && $model->$moneyline < self::MONEYLINE['under']['max'])
                ) continue;

                $events[] = $model;
            }
        }

        return ArrayHelper::getColumn($events, 'id');
    }

    /**
     * @param Event[] $models
     * @param string $type
     * @return array
     */
    public static function getEventsStat(array $models, string $type = Odd::ADD_TYPE['over']): array
    {
        $method = ($type == Odd::ADD_TYPE['over']) ? 'totalOverStat' : 'totalUnderStat';
        $data = [];
        foreach ($models as $model) {
            foreach (array_keys(TotalHelper::ODDS) as $i) {
                $statField = "profit_$i";
                $profit = $model->$method->$statField ?? null;
                if (is_null($profit)) continue;

                /** get profit */
                $data[$i]['profit'] = !isset($data[$i]['profit'])
                    ? $profit
                    : $data[$i]['profit'] + $profit;

                /** get count */
                $data[$i]['count'] = !isset($data[$i]['count'])
                    ? 1
                    : $data[$i]['count'] + 1
                ;
            }
        }

        ksort($data);
        return $data;
    }

    /**
     * @param string $type
     * @return array
     */
    public static function getPlayers(string $type): array
    {
        $players = (new Query())
            ->select([
                'tn_player.id player_id',
                'tn_player.name player',
                'tn_tournament.name tournament',
                'tn_event.id event_id',
                'tn_event.start_at event_start',
                'tn_event.sofa_id event_sofa_id',
                'tn_round.name round',
                'player_home.name player_home',
                'player_away.name player_away',
                'moneyline_home.odd moneyline_home_odd',
                'moneyline_away.odd moneyline_away_odd',
            ])
            ->from('tn_player_total')
            ->leftJoin('tn_player', 'tn_player_total.player_id = tn_player.id')
            ->leftJoin('tn_event', 'tn_player.id = tn_event.home or tn_player.id = tn_event.away')
            ->leftJoin('tn_event event_winner', 'tn_player.id = event_winner.winner and tn_event.id = event_winner.id')
            ->leftJoin('tn_tournament', 'tn_event.tournament = tn_tournament.id and tn_player_total.tour_id = tn_tournament.tour and tn_player_total.surface_id = tn_tournament.surface')
            ->leftJoin('tn_round', 'tn_round.id = tn_event.round')
            ->leftJoin('tn_player player_home', 'tn_event.home = player_home.id')
            ->leftJoin('tn_player player_away', 'tn_event.away = player_away.id')
            ->leftJoin('sp_odd moneyline_home', 'tn_event.id = moneyline_home.event and tn_event.home = moneyline_home.player_id and moneyline_home.type = ' .  Odd::TYPE['moneyline'])
            ->leftJoin('sp_odd moneyline_away', 'tn_event.id = moneyline_away.event and tn_event.away = moneyline_away.player_id and moneyline_away.type = ' .  Odd::TYPE['moneyline'])
            ->where(['tn_player_total.type' => $type])
            ->andWhere(['IS NOT', 'tn_tournament.id', null])
            ->andWhere('tn_event.id = (SELECT MAX(id) FROM tn_event event_id WHERE (tn_player.id = event_id.home or tn_player.id = event_id.away))')
            ->andWhere('(tn_event.sofa_id is null or (event_winner.winner = tn_player_total.player_id and event_winner.round != 3))')
            ->orderBy(['tn_tournament.name' => SORT_ASC])
            //->groupBy('tn_player_total.player_id')
            ->all()
        ;

        /** prepare data */
        foreach ($players as $k => $player) {

            /** get event data */
            $player['event_start'] = (is_null($player['event_sofa_id'])) ? date('d.m H:i', strtotime($player['event_start'])) : 'none';
            $player['round'] = (is_null($player['event_sofa_id'])) ? $player['round'] : 'none';
            $player['event'] = (is_null($player['event_sofa_id'])) ? "{$player['player_home']} - {$player['player_away']}" : 'none';

            /** get moneyline */
            $player['moneyline_home_odd'] = isset($player['moneyline_home_odd']) ? $player['moneyline_home_odd']/100 : 0;
            $player['moneyline_away_odd'] = isset($player['moneyline_away_odd']) ? $player['moneyline_away_odd']/100 : 0;
            $player['moneyline'] = (is_null($player['event_sofa_id'])) ? "{$player['moneyline_home_odd']} - {$player['moneyline_away_odd']}" : 'none';

            /** get links */
            $player['link'] = [
                '/statistic/total/events',
                'model' => 'EventTotalSearch',
                'EventTotalSearch[player]' => $player['player']
            ];
            $player['tournament_link'] = [
                '/event/index',
                'model' => 'EventSearch',
                'EventSearch[tournament_name]' => $player['tournament']
            ];
            $player['event_link'] = [
                'event/view',
                'id' => $player['event_id']
            ];

            $players[$k] = $player;
        }

        return $players;
    }

}