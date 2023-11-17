<?php


namespace backend\helpers\total;

use backend\models\statistic\FilterModel;
use common\helpers\EventFilterHelper;
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
    public static function getEventPlayerStat(Event $event)
    {
        $query = self::getQuery($event, 1);
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
    public static function getQuery(Event $event, int $sort = 2): string
    {
        $sql = "SELECT player_main.name, " . self::getSubQuery($event) . "
                FROM `tn_player` player_main
                HAVING profit{$sort} IS NOT NULL 
                ORDER BY profit{$sort} DESC;";

        return $sql;
    }

    /**
     * @param Event $event
     * @return string
     */
    public static function getSubQuery(Event $event): string
    {
        $minMoneyline = 140;
        $odds = OddHelper::totalSettings();
        sort($odds);

        $sql = "";
        foreach ($odds as $k => $odd) {
            $where = "player_profit{$k}.id = player_main.id";
            $where .= " and `home_moneyline`.`odd` >= {$minMoneyline} and `away_moneyline`.odd >= {$minMoneyline}";
            $where .= " and tournament.tour = {$event->eventTournament->tour} and tournament.surface = {$event->eventTournament->surface}";
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

}