<?php


namespace common\helpers;

use backend\components\pinnacle\helpers\BaseHelper;
use frontend\models\sport\Event;
use frontend\models\sport\Round;
use frontend\models\sport\Surface;
use frontend\models\sport\Tour;
use frontend\models\sport\Tournament;

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
        $odds = [0, 20, 40, 60, 80, 100, 33, 67, self::STAT_EMPTY_KEY,];
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
        $settings = [100 => 1, 80 => 2, 60 => 3, 40 => 4, 20 => 5];
        $settingsTwo = [60 => 1, 40 => 2, 20 => 3];

        $settingsEvents = [
            100 => [80],
            80 => [100, 60],
            60 => [100, 80, 40],
            40 => [100, 80, 60, 20],
            20 => [100, 80, 60, 40, 0],
        ];

        $eventsArr[100] = array_merge($data[100]['events'], $data[80]['events']);
        $eventsArr[80] = array_merge($data[100]['events'], $data[80]['events'], $data[60]['events']);
        $eventsArr[60] = array_merge($data[100]['events'], $data[80]['events'], $data[60]['events'], $data[40]['events']);
        $eventsArr[40] = array_merge($data[100]['events'], $data[80]['events'], $data[60]['events'], $data[40]['events'], $data[20]['events']);
        $eventsArr[20] = array_merge($data[100]['events'], $data[80]['events'], $data[60]['events'], $data[40]['events'], $data[20]['events'], $data[0]['events']);

        foreach ($eventsArr as $percent => $events) {
            $profit = 0;
            foreach ($events as $event) {

                $key = '-';
                if(count($event->{$type}) == 5) {
                    $key = count($event->{$type}) - $settings[$percent];
                }
                else if(count($event->{$type}) == 3) {
                    if(!isset($settingsTwo[$percent])) continue;
                    $key = count($event->{$type}) - $settingsTwo[$percent];
                }
                $profit += $event->{$type}[$key]->profit;
                //if($percent == 60) echo $key .' '. $percent. ' ' . $event->id . ' - ' . $event->{$type}[$key]->profit . '<br>';
            }
            if($percent == 100) {
                $count_events = count($data[0]['events']) + count($data[20]['events']) + count($data[40]['events']) + count($data[60]['events']);
                $data[$percent]['profit'] = $profit - $count_events  * 100;
                //$data[$percent]['profit'] = $profit;
            }

            else if($percent == 80) {
                $count_events = count($data[0]['events']) + count($data[20]['events']) + count($data[40]['events']);
                $data[$percent]['profit'] = $profit - $count_events  * 100;
                //$data[$percent]['profit'] = $count_events;
            }

           else if($percent == 60) {
               $count_events = count($data[0]['events']) + count($data[20]['events']);
               $data[$percent]['profit'] = $profit - $count_events  * 100;
               //$data[$percent]['profit'] = $profit;
           }
           else if($percent == 40) {
               $count_events = count($data[0]['events']);
               $data[$percent]['profit'] = $profit - $count_events * 100;
           }

           else if($percent == 20) {
               $data[$percent]['profit'] = $profit;
           }
            else $data[$percent]['profit'] = 0;
        }

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

    /**
     * @param Tournament $tournament
     * @return array
     */
    public static function getEventsOdds(Tournament $tournament): array
    {
        $odds = [];
        foreach ($tournament->events as $event) {
            $odds = array_merge($odds, EventHelper::getOdds($event));
        }

        return $odds;
    }
}