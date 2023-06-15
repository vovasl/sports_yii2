<?php


namespace common\helpers;

class TournamentHelper
{

    /**
     * @param array $events
     * @param $type
     * @return array
     */
    public static function getOddStat(array $events, $type): array
    {
        $data = [];
        foreach ($events as $event) {
            if(is_null($event->winner)) continue;
            $key = EventHelper::getOddStatPercent($event->{$type});
            $data[$key]++;
        }
        ksort($data);

        return $data;
    }
}