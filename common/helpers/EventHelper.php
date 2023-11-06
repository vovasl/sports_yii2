<?php


namespace common\helpers;

use frontend\models\sport\Event;
use frontend\models\sport\Round;

class EventHelper
{

    /**
     * @return string[]
     */
    public static function resultDropdown(): array
    {
        return [
            1 => 'Yes',
            2 => 'No'
        ];
    }

    /**
     * @return int[]
     */
    public static function setsDropdown(): array
    {
        $values = range(2, 5);
        return array_combine($values, $values);
    }

    /**
     * @param array $odds
     * @return string
     */
    public static function getOddStat(array $odds): string
    {
        $data = self::_getOddStat($odds);
        return $data['count'] > 0 ? "{$data['val']}/{$data['count']}" : "";
    }

    /**
     * @param array $odds
     * @return int
     */
    public static function getOddStatPercent(array $odds): int
    {
        $data = self::_getOddStat($odds);
        return $data['count'] > 0 ? round($data['val'] / $data['count'] * 100) : 0;
    }

    /**
     * @param array $odds
     * @return array
     */
    public static function _getOddStat(array $odds): array
    {
        $data = ['val' => 0, 'count' => 0];
        foreach($odds as $odd) {
            if(is_null($odd->profit)) continue;
            if($odd->profit > 0) $data['val']++;
            $data['count']++;
        }

        return $data;
    }

    /**
     * @param array $events
     * @param int $qualifier
     * @return int
     */
    public static function getCount(array $events, int $qualifier = -1): int
    {
        $count = 0;
        foreach ($events as $event) {
            if($qualifier == -1 && $event->round != Round::QUALIFIER) $count++;
            else if($qualifier == 1 && $event->round == Round::QUALIFIER) $count++;
        }
        return $count;
    }

    /**
     * @param $val
     * @return string
     */
    public static function getQualifierText($val): string
    {
        switch ($val) {
            case 0:
                $text = "Main";
                break;
            case 1:
                $text = "Qualifiers";
                break;
            default:
                $text = "ALL";
                break;
        }

        return $text;
    }

    /**
     * @return array
     */
    public static function gridHomePlayer(): array
    {
        return [
            'label' => 'Event',
            'attribute' => 'player',
            'format' => 'raw',
            'value' => function(Event $model) {
                return $model->outputPlayer('homePlayer');
            },
            'headerOptions' => [
                'colspan' => 2,
                'style' => 'text-align: center;'
            ],
            'filterOptions' => [
                'colspan' => 2,
            ],
        ];
    }

    /**
     * @return array
     */
    public static function gridAwayPlayer(): array
    {
        return [
            'format' => 'raw',
            'value' => function(Event $model) {
                return $model->outputPlayer('awayPlayer');
            },
            'headerOptions' => [
                'style' => 'display: none;',
            ],
            'filterOptions' => [
                'style' => 'display: none;',
            ]
        ];
    }

    /**
     * @param Event $event
     * @return array
     */
    public static function getOdds(Event $event): array
    {
        $odds = [];
        foreach ($event->odds as $odd) {
            $odds[] = $odd;
        }

        return $odds;
    }

    /**
     * @param array $odds
     * @return string
     */
    public static function getTotal(array $odds): string
    {
        $key = round(count($odds)/2);
        return (isset($odds[$key])) ? $odds[$key]->value : '';
    }

}