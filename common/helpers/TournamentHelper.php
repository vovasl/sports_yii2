<?php


namespace common\helpers;

use backend\components\pinnacle\helpers\BaseHelper;
use frontend\models\sport\Event;
use frontend\models\sport\Round;
use frontend\models\sport\Surface;
use frontend\models\sport\Tour;

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
        $data = self::initOddStatData();

        $count = 0;
        foreach ($events as $event) {

            if(is_null($event->winner)) continue;
            if($qualifier == -1 && $event->round == Round::QUALIFIER) continue;
            if($qualifier == 1 && $event->round != Round::QUALIFIER) continue;

            $key = (count($event->{$type}) > 0) ? EventHelper::getOddStatPercent($event->{$type}) : self::STAT_EMPTY_KEY;
            $data[$key]['events'][] = $event;

            $count++;
        }

        $count -= count($data[self::STAT_EMPTY_KEY]['events']);

        $data = self::prepareOddStat($data, $count);

        $data = self::getOddStatProfit($data, $type, $count);

        //BaseHelper::outputArray($data); die;
        return $data;
    }

    /**
     * @return array
     */
    public static function initOddStatData(): array
    {
        $data = [];
        $odds = [0, 20, 33, 40, 60, 67, 80, 100, self::STAT_EMPTY_KEY];
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

        $emptyKey = self::STAT_EMPTY_KEY;
        if($count == 0) return $data;
        array_walk($data, function (&$val, $key) use ($count, $emptyKey) {
            $val['percent'] = ($key !== $emptyKey) ? round(count($val['events']) / $count * 100) : ' - ';
        });

        //ksort($data);

        return $data;
    }

    public static function getOddStatProfit($data, $type, $count)
    {
        $settings = [100 => 1, 80 => 2, 60 => 3, 40 => 4, 20 => 5, 67 => 1, 33 => 2];

        $eventsArr = [];
        foreach ($data as $percent => $stat) {
            if($percent == 100) {
                $eventsArr[$percent] = array_merge($data[100]['events'], $data[80]['events']);
            }
            else if($percent == 80) {
                $eventsArr[$percent] = array_merge($data[100]['events'], $data[80]['events'], $data[60]['events']);
            }
            else if($percent == 67) {}
            else if($percent == 60) {
                $eventsArr[$percent] = array_merge($data[100]['events'], $data[80]['events'], $data[67]['events'], $stat['events']);
            }
            else if($percent == 40) {
                $eventsArr[$percent] = array_merge($data[100]['events'], $data[80]['events'], $data[67]['events'], $data[60]['events'], $stat['events']);
            }
            else if($percent == 33) {}
            else if($percent == 20) {
                $eventsArr[$percent] = array_merge($data[100]['events'], $data[80]['events'], $data[67]['events'], $data[60]['events'], $data[40]['events'], $stat['events']);
            }
        }

        foreach ($eventsArr as $percent => $events) {
            $profit = 0;
            foreach ($events as $event) {
                $key = count($event->{$type}) - $settings[$percent];
                $profit += $event->{$type}[$key]->profit;
            }
            if($percent == 100) {
                $count_events = count($data[0]['events']) + count($data[20]['events']) + count($data[33]['events']) + count($data[40]['events']) + count($data[60]['events']);
                $data[$percent]['profit'] = $profit - $count_events  * 100;
            }
            else if($percent == 80 || $percent == 67) {
                $count_events = count($data[0]['events']) + count($data[20]['events']) + count($data[33]['events']) + count($data[40]['events']) + count($data[60]['events']);
                $data[$percent]['profit'] = $profit - $count_events  * 100;
            }
            else if($percent == 60) {
                $count_events = count($data[0]['events']) + count($data[20]['events']) + count($data[33]['events']) + count($data[40]['events']);
                $data[$percent]['profit'] = $profit - $count_events  * 100;
            }
            else if($percent == 40) {
                $count_events = count($data[0]['events']) + count($data[20]['events']) + count($data[33]['events']);
                $data[$percent]['profit'] = $profit -  $count_events * 100;
            }
            else if($percent == 20 || $percent == 33) {
                $data[$percent]['profit'] = $profit - count($data[0]['events']) * 100;
            }
            else $data[$percent]['profit'] = 0;
        }

        /*
        foreach ($data as $percent => $stat) {
            if($percent === 0 || $percent == self::STAT_EMPTY_KEY) {
                $data[$percent]['profit_2'] = 0;
                continue;
            }
            $profit = 0;
            foreach ($stat['events'] as $event) {
                $key = count($event->{$type}) - $settings[$percent];
                $profit += $event->{$type}[$key]->profit;
            }
            if($percent == 100) $data[$percent]['profit_2'] = $profit - ($count - count($data[100]['events'])) * 100;
            else $data[$percent]['profit_2'] = $profit;
            //if($percent == 20) $data[$percent]['profit_2'] = $profit - (count($data[0]['events']) * 100);
        }
        */

        return $data;
    }

    /**
     * @param int|null $tour
     * @param int|null $surface
     * @return string
     */
    public static function getTourSurfaceTitle(int $tour = null, int $surface = null): string
    {
        $title = [];
        if(!is_null($tour) && $tour = Tour::findOne($tour)) {
            $title[] = $tour->name;
        }

        if(!is_null($surface) && $surface = Surface::findOne($surface)) {
            $title[] = $surface->name;
        }

        return implode(', ', $title);
    }

    /**
     * @param array $tournaments
     * @return int
     */
    public static function getEventsCount(array $tournaments): int
    {
        $count = 0;
        foreach ($tournaments as $tournament) {
            $count += $tournament->count_events;
        }

        return $count;
    }
}