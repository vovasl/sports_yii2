<?php

namespace common\helpers\total;


use common\helpers\TotalHelper;
use frontend\models\sport\Event;
use frontend\models\sport\Odd;
use frontend\models\sport\Player;
use frontend\models\sport\PlayerTotal;
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

            /** get events */
            switch ($type) {
                case Odd::ADD_TYPE['over']:
                    $query = self::getWhereOver($query, ArrayHelper::getColumn($playersTotal, 'player_id'));
                    $events = ArrayHelper::getColumn($query->all(), 'id');
                    break;
                case Odd::ADD_TYPE['under']:
                    $query = self::getWhereUnder($query, ArrayHelper::getColumn($playersTotal, 'player_id'));
                    $events = ArrayHelper::getColumn($query->all(), 'id');
                    break;
                case PlayerTotal::TYPE['over-favorite']:
                case PlayerTotal::TYPE['under-favorite']:
                    $query = self::getWhereFavorite($query, ArrayHelper::getColumn($playersTotal, 'player_id'));
                    $events = self::getEventsFavorite($query->all(), $playersTotal);
                    break;
                default:
                    $events = [];
                    break;
            }

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
        $model->andWhere(['>=', 'home_moneyline.odd', self::MONEYLINE['over']['min']]);
        $model->andWhere(['>=', 'away_moneyline.odd', self::MONEYLINE['over']['min']]);

        return $model;
    }

    /**
     * @param ActiveQuery $model
     * @param array $players
     * @return ActiveQuery
     */
    public static function getWhereFavorite(ActiveQuery $model, array $players): ActiveQuery
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
    public static function getEventsFavorite(array $models, array $playersTotal): array
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
     * @param array $type
     * @return array
     */
    public static function getPlayers(array $type): array
    {
        $players = (new Query())
            ->select([
                'tn_player.id player_id',
                'tn_player_total.favorite favorite',
                'tn_player.name player',
                'tn_event.id event_id',
                'tn_event.start_at event_start',
                'tn_event.sofa_id event_sofa_id',
                'tn_round.name round',
                'player_home.name player_home',
                'player_away.name player_away',
                'moneyline_home.odd moneyline_home_odd',
                'moneyline_away.odd moneyline_away_odd',
                'avg(total_over.value) total_avg_value',
                'tn_tournament.name tournament',
                'tn_tour.name tour',
                'tn_surface.name surface',
            ])
            ->from('tn_player_total')
            ->leftJoin('tn_player', 'tn_player_total.player_id = tn_player.id')
            ->leftJoin('tn_event', 'tn_player.id = tn_event.home or tn_player.id = tn_event.away')
            ->leftJoin('tn_event event_winner', 'tn_player.id = event_winner.winner and tn_event.id = event_winner.id')
            ->leftJoin('tn_round', 'tn_round.id = tn_event.round')
            ->leftJoin('tn_tournament', 'tn_event.tournament = tn_tournament.id and tn_player_total.tour_id = tn_tournament.tour and tn_player_total.surface_id = tn_tournament.surface')
            ->leftJoin('tn_tour', 'tn_tournament.tour = tn_tour.id')
            ->leftJoin('tn_surface', 'tn_tournament.surface = tn_surface.id')
            ->leftJoin('tn_player player_home', 'tn_event.home = player_home.id')
            ->leftJoin('tn_player player_away', 'tn_event.away = player_away.id')
            ->leftJoin('sp_odd moneyline_home', 'tn_event.id = moneyline_home.event and tn_event.home = moneyline_home.player_id and moneyline_home.type = ' .  Odd::TYPE['moneyline'])
            ->leftJoin('sp_odd moneyline_away', 'tn_event.id = moneyline_away.event and tn_event.away = moneyline_away.player_id and moneyline_away.type = ' .  Odd::TYPE['moneyline'])
            ->leftJoin('sp_odd total_over', 'tn_event.id = total_over.event and total_over.type = ' . Odd::TYPE['totals'] . ' and total_over.add_type = \'' . Odd::ADD_TYPE['over'] . '\'')
            ->where(['IN', 'tn_player_total.type', $type])
            ->andWhere(['IS NOT', 'tn_tournament.id', null])
            ->andWhere('tn_event.id = (SELECT id FROM tn_event event_id WHERE tn_player.id = event_id.home or tn_player.id = event_id.away order by event_id.start_at DESC limit 1)')
            ->andWhere('(tn_event.sofa_id is null or (event_winner.winner = tn_player_total.player_id and event_winner.round != 3))')
            ->orderBy([
                'tn_tournament.name' => SORT_ASC,
                'event_start' => SORT_ASC,
                'event_id' => SORT_ASC,
            ])
            ->groupBy('tn_player_total.player_id')
            ->all()
        ;

        /** prepare data */
        foreach ($players as $k => $player) {

            /** get player total data */
            $player['favorite'] = ($player['favorite']) ? 'Yes' : '';

            /** get event data */
            $player['event_start'] = (is_null($player['event_sofa_id'])) ? date('d.m H:i', strtotime($player['event_start'])) : 'none';
            $player['round'] = (is_null($player['event_sofa_id'])) ? $player['round'] : 'none';
            $player['event'] = (is_null($player['event_sofa_id'])) ? "{$player['player_home']} - {$player['player_away']}" : 'none';

            /** get moneyline */
            $player['moneyline_home_odd'] = isset($player['moneyline_home_odd']) ? $player['moneyline_home_odd']/100 : 0;
            $player['moneyline_away_odd'] = isset($player['moneyline_away_odd']) ? $player['moneyline_away_odd']/100 : 0;
            $player['moneyline'] = (is_null($player['event_sofa_id'])) ? "{$player['moneyline_home_odd']} - {$player['moneyline_away_odd']}" : 'none';

            /** get total */
            $player['total_avg_value'] = (is_null($player['event_sofa_id'])) ? $player['total_avg_value'] : 'none';

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

    /**
     * @param Event $event
     * @param string $player
     * @param $tour
     * @param $surface
     * @return array
     */
    public static function getEventsPlayerNotOver(Event $event, string $player, $tour, $surface): array
    {
        return (new Query())
            ->select([
                'tn_event.id'
            ])
            ->from('tn_player_total')
            ->leftJoin('tn_event', 'tn_player_total.player_id = tn_event.home or tn_player_total.player_id = tn_event.away')
            ->leftJoin('tn_tournament', 'tn_event.tournament = tn_tournament.id')
            ->leftJoin('sp_odd home_moneyline', 'tn_event.id = home_moneyline.event AND tn_event.home = home_moneyline.player_id AND home_moneyline.type = ' . Odd::TYPE['moneyline'])
            ->leftJoin('sp_odd away_moneyline', 'tn_event.id = away_moneyline.event AND tn_event.away = away_moneyline.player_id AND away_moneyline.type = ' . Odd::TYPE['moneyline'])
            ->where(['IN', 'tn_player_total.tour_id', $tour])
            ->andWhere(['IN', 'tn_player_total.surface_id', $surface])
            ->andWhere(['tn_player_total.type' => Odd::ADD_TYPE['over']])
            ->andWhere(['OR',
                ['tn_event.home' => $event->{$player}],
                ['tn_event.away' => $event->{$player}]
            ])
            ->groupBy('tn_event.id')
            ->column()
            ;
    }

}