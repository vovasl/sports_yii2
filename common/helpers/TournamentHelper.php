<?php


namespace common\helpers;

use backend\components\pinnacle\helpers\BaseHelper;
use frontend\models\sport\Round;

class TournamentHelper
{

    /**
     * @param array $events
     * @param $type
     * @param int $qualifier
     * @return array
     */

    public static function getOddStat(array $events, $type, int $qualifier = 0): array
    {
        $emptyKey = 'no';
        $data = [0 => [], 20 => [], 40 => [], 60 => [], 80 => [], 100 => [], $emptyKey => []];
        array_walk($data, function (&$val) {
            $val['events'] = [];
        });

        $count = 0;
        foreach ($events as $event) {

            if(is_null($event->winner)) continue;
            if($qualifier == -1 && $event->round == Round::QUALIFIER) continue;
            if($qualifier == 1 && $event->round != Round::QUALIFIER) continue;

            $key = (count($event->{$type}) > 0) ? EventHelper::getOddStatPercent($event->{$type}) : $emptyKey;
            $data[$key]['events'][] = $event['id'];
            $count++;
        }

        $count -= count($data[$emptyKey]['events']);
        array_walk($data, function (&$val, $key) use ($count, $emptyKey) {
            $val['percent'] = ($key !== $emptyKey) ? round(count($val['events']) / $count * 100) : ' - ';
        });

        ksort($data);

        return $data;
    }
}