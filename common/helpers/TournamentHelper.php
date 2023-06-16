<?php


namespace common\helpers;

use backend\components\pinnacle\helpers\BaseHelper;
use frontend\models\sport\Round;

class TournamentHelper
{

    CONST STAT_EMPTY_KEY = 'NO';

    /**
     * @param array $events
     * @param $type
     * @param int $qualifier
     * @return array
     */
    public static function getOddStat(array $events, $type, int $qualifier = 0): array
    {
        $data = self::prepareOddStatData();

        $count = 0;
        foreach ($events as $event) {

            if(is_null($event->winner)) continue;
            if($qualifier == -1 && $event->round == Round::QUALIFIER) continue;
            if($qualifier == 1 && $event->round != Round::QUALIFIER) continue;

            $key = (count($event->{$type}) > 0) ? EventHelper::getOddStatPercent($event->{$type}) : self::STAT_EMPTY_KEY;
            $data[$key]['events'][] = $event['id'];
            $count++;
        }

        $data = self::prepareOddStat($data, $count);

        return $data;
    }

    /**
     * @return array
     */
    public static function prepareOddStatData(): array
    {
        $data = [];
        $odds = [0, 20, 40, 60, 80, 100, self::STAT_EMPTY_KEY];
        array_walk( $odds, function (&$val) use (&$data) {
            $data[$val]['events'] = [];
        });

        return $data;
    }

    /**
     * @param $data
     * @param $count
     * @return array
     */
    public static function prepareOddStat($data, $count): array
    {
        $count -= count($data[self::STAT_EMPTY_KEY]['events']);
        $emptyKey = self::STAT_EMPTY_KEY;

        if($count == 0) return $data;

        array_walk($data, function (&$val, $key) use ($count, $emptyKey) {
            $val['percent'] = ($key !== $emptyKey) ? round(count($val['events']) / $count * 100) : ' - ';
        });

        //ksort($data);

        return $data;
    }
}