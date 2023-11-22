<?php


namespace backend\helpers\total;

use backend\models\statistic\FilterModel;
use common\helpers\EventFilterHelper;
use common\helpers\EventHelper;
use common\helpers\OddHelper;
use frontend\models\sport\Event;
use frontend\models\sport\Odd;
use frontend\models\sport\OddType;
use Yii;
use yii\db\DataReader;
use yii\db\Exception;

class OverHelper
{

    /**
     * @return array
     */
    public static function ATPHard(): array
    {
        return [
            'tour' => 1,
            'surface' => 2,
            'rounds' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 11],
            'value' => 21.5,
            'odds' => [
                'min' => 167,
                'max' => 300
            ],
            'moneyline' => [
                'filter' => EventFilterHelper::MONEYLINE_FILTER['more'],
                'limit' => 150
            ]
        ];
    }

    /**
     * @return array
     */
    public static function ATPIndoor(): array
    {
        return [
            'tour' => 1,
            'surface' => 4,
            'rounds' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 11],
            'value' => 21.5,
            'odds' => [
                'min' => 167,
                'max' => 300
            ],
            'moneyline' => [
                'filter' => EventFilterHelper::MONEYLINE_FILTER['more'],
                'limit' => 150
            ]
        ];
    }

    /**
     * @return array
     */
    public static function challengerClay(): array
    {
        return [
            'tour' => 2,
            'surface' => 1,
            'rounds' => [1, 2, 3, 4, 6, 7, 8, 9, 11],
            'value' => 20.5,
            'odds' => [
                'min' => 165,
                'max' => 300
            ],
            'moneyline' => [
                'filter' => EventFilterHelper::MONEYLINE_FILTER['more'],
                'limit' => 150
            ]
        ];
    }

    /**
     * @return array
     */
    public static function challengerHard(): array
    {
        return [
            'tour' => 2,
            'surface' => 2,
            'rounds' => [1, 2, 3, 4, 6, 7, 8, 9, 11],
            'value' => 21.5,
            'odds' => [
                'min' => 165,
                'max' => 300
            ],
            'moneyline' => [
                'filter' => EventFilterHelper::MONEYLINE_FILTER['more'],
                'limit' => 150
            ]
        ];
    }

    /**
     * @return array
     */
    public static function challengerIndoor(): array
    {
        return [
            'tour' => 2,
            'surface' => 4,
            'rounds' => [1, 2, 3, 4, 6, 7, 8, 9, 11],
            'value' => 21.5,
            'odds' => [
                'min' => 165,
                'max' => 300
            ],
            'moneyline' => [
                'filter' => EventFilterHelper::MONEYLINE_FILTER['more'],
                'limit' => 150
            ]
        ];
    }

    /**
     * @param Event $event
     * @return array|DataReader
     * @throws Exception
     */
    public static function getEventPlayersStat(Event $event)
    {
        $query = self::getEventPlayersQuery($event, 1);
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand($query);
        $result = $command->queryAll();

        return $result;
    }

    /**
     * @param Event $event
     * @param int $sort
     * @return string
     */
    public static function getEventPlayersQuery(Event $event, int $sort = 2): string
    {
        $sql = "SELECT player_main.name, " . self::getEventPlayersSubQuery($event) . "
                FROM `tn_player` player_main
                HAVING profit{$sort} IS NOT NULL 
                ORDER BY FIELD(player_main.name, \"{$event->homePlayer->name}\", \"{$event->awayPlayer->name}\")
                ;";

        return $sql;
    }

    /**
     * @param Event $event
     * @return string
     */
    public static function getEventPlayersSubQuery(Event $event): string
    {
        $minMoneyline = 140;
        $surface = (in_array($event->eventTournament->surface, [2, 4])) ? '2,4' : $event->eventTournament->surface;
        $odds = OddHelper::totalSettings();
        sort($odds);

        $sql = "";
        foreach ($odds as $k => $odd) {
            $where = "player_profit{$k}.id = player_main.id";
            $where .= " and event.five_sets = 0";
            $where .= " and odd_total_over.profit IS NOT NULL";
            $where .= " and `home_moneyline`.`odd` >= {$minMoneyline} and `away_moneyline`.odd >= {$minMoneyline}";
            //$where .= " and tournament.tour = {$event->eventTournament->tour}";
            $where .= " and tournament.surface IN ({$surface})";
            /** odd where */
            if(!isset($odds[$k + 1])) $where .= " and odd_total_over.odd >= {$odd}";
            else $where .= " and odd_total_over.odd >= {$odd} and odd_total_over.odd < {$odds[$k + 1]}";
            /** player where */
            $where .= " and (player_main.name = '" . $event->homePlayer->name . "' or player_main.name = '" . $event->awayPlayer->name . "')";
            $sql .= "(
    	        SELECT 
    	            sum(odd_total_over.profit)
		        FROM 
		            `tn_player` player_profit{$k}
		        LEFT JOIN `tn_event` event ON (event.home = player_profit{$k}.id OR event.away = player_profit{$k}.id)
                LEFT JOIN `tn_tournament` tournament ON (tournament.id = event.tournament)
		        LEFT JOIN `sp_odd` odd_total_over ON (event.id = odd_total_over.event and odd_total_over.type = " . Odd::TYPE['totals'] . " and odd_total_over.add_type = '" . Odd::ADD_TYPE['over'] . "')        
                LEFT JOIN `sp_odd` `home_moneyline` ON (`event`.`id` = `home_moneyline`.`event` AND `event`.`home` = `home_moneyline`.`player_id`) AND (`home_moneyline`.`type` = 4) 
		        LEFT JOIN `sp_odd` `away_moneyline` ON (`event`.`id` = `away_moneyline`.`event` AND `event`.`away` = `away_moneyline`.`player_id`) AND (`away_moneyline`.`type` = 4)
		        WHERE {$where}
		        ) profit{$k}";
            if(isset($odds[$k + 1])) $sql .= ", ";
        }

        return $sql;
    }

    /**
     * @param Event $event
     * @return string
     * @throws Exception
     */
    public static function getEventPlayersGeneralStat(Event $event): string
    {
        $minKoef = 0;
        $query = self::getEventPlayersGeneralQuery($event);
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand($query);
        $stats = $command->queryAll();

        $output = "";
        if(count($stats) != 2) return $output;

        foreach ($stats as $stat) {
            $koef = round($stat['profit'] / $stat['count_odds']);
            if($koef < $minKoef) return '';
            $output .= "{$koef} ";
        }

        /** totalOver output markers */
        $totalOver = EventHelper::getOddStat($event->totalsOver);
        if(in_array($totalOver, ['5/5', '7/7', '4/5', '3/5'])) $output .= 'QQQQQ';
        else if($totalOver == '0/5') $output .= 'WWWWW';
        else if($totalOver == '1/5') $output .= 'EEEEE';
        else if($totalOver == '2/5') $output .= 'RRRRR';
        else $output .= 'TTTTT';

        return $output;
    }

    /**
     * @param Event $event
     * @return string
     */
    public static function getEventPlayersGeneralQuery(Event $event): string
    {
        $surface = (in_array($event->eventTournament->surface, [2, 4])) ? '2,4' : $event->eventTournament->surface;
        $where = "`event`.five_sets = 0 and event.sofa_id is not null";
        $where .= " and `home_moneyline`.`odd` >= 140 and `away_moneyline`.odd >= 140 and event.five_sets = 0";
        //$where .= " and tournament.tour = {$event->eventTournament->tour}";
        $where .= " and tournament.surface IN ($surface)";
        $where .= ' and (player.name = "' . $event->homePlayer->name . '" or player.name = "' . $event->awayPlayer->name . '")';

        $sql = "SELECT 
                    player.id, player.name, sum(odd_total_over.profit) profit, count(odd_total_over.id) count_odds
                FROM 
                    `tn_player` player
                LEFT JOIN `tn_event` event ON (event.home = player.id OR event.away = player.id)
                LEFT JOIN `tn_tournament` tournament ON (tournament.id = event.tournament)
                LEFT JOIN `sp_odd` odd_total_over ON (event.id = odd_total_over.event and odd_total_over.type = 2 and odd_total_over.add_type = 'over')
                LEFT JOIN `sp_odd` `home_moneyline` ON (`event`.`id` = `home_moneyline`.`event` AND `event`.`home` = `home_moneyline`.`player_id`) AND (`home_moneyline`.`type` = 4) 
                LEFT JOIN `sp_odd` `away_moneyline` ON (`event`.`id` = `away_moneyline`.`event` AND `event`.`away` = `away_moneyline`.`player_id`) AND (`away_moneyline`.`type` = 4)
                WHERE 
                      {$where}
                GROUP BY 
                         player.id
                HAVING 
                       count_odds >= 50
                ORDER BY 
                         FIELD(player.name, \"{$event->homePlayer->name}\", \"{$event->awayPlayer->name}\")
                ";

        return $sql;
    }

}