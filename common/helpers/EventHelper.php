<?php


namespace common\helpers;

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

}