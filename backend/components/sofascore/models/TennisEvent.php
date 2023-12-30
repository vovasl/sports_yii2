<?php

namespace backend\components\sofascore\models;


use backend\components\pinnacle\helpers\BaseHelper;

class TennisEvent
{

    /** allowed tournament categories */
    CONST TOUR = [
        3,
        6,
        72,
        //76, // Davis Cup
        1705  // United Cup
    ];

    /** allowed status codes */
    CONST STATUS_CODES = [100, 98, 92, 91];

    /**
     * @var array
     */
    private $events;

    /**
     * TennisEvent constructor.
     * @param array $events
     */
    public function __construct(array $events)
    {
        $this->events = $events;
    }

    /**
     * @return array
     */
    public function getEvents(): array
    {

        /** filter events */
        $this->filter();

        /** prepare events result */
        $this->prepareResult();

        return $this->events;
    }


    private function filter()
    {
        /** tournament filter */
        $this->events = array_filter($this->events, function ($event) {
            return (in_array($event['tournament']['category']['id'], self::TOUR)
                && !preg_match('#double|doubles|mixed|Finals, Group#i', $event['tournament']['name']));
        });

        /** status code filter */
        $this->events = array_filter($this->events, function ($event) {
            return in_array($event['status']['code'], self::STATUS_CODES);
        });
    }

    private function prepareResult()
    {
        foreach ($this->events as $k => $event) {
            $res = [];
/*            if(!isset($event['homeScore']['display'])) {
                BaseHelper::outputArray($event);
                die;
            }*/
            $res['sets'] = [
                $event['homeScore']['display'] ?? 0,
                $event['awayScore']['display'] ?? 0
            ];

            for ($set = 1; $set <= 5; $set++) {
                if(isset($event['homeScore']['period' . $set]) && isset($event['awayScore']['period' . $set])) {
                    $res['games'][$set] = [$event['homeScore']['period' . $set], $event['awayScore']['period' . $set]];
                }
            }
            $this->events[$k]['result'] = $res;
        }
    }

    /**
     * @param array $event
     * @return string
     */
    public static function output(array $event): string
    {
        $games = [];
        if(isset($event['result']['games'])) {
            foreach ($event['result']['games'] as $res) {
                $games[] = implode(":", $res);
            }
        }

        $output = "{$event['tournament']['name']} ";
        if(isset($event['roundInfo']['name'])) $output .= ", {$event['roundInfo']['name']}";
        $output .= "<br>" . date('d.m H:i' ,$event['startTimestamp']);
        $output .= " {$event['homeTeam']['name']} ({$event['homeTeam']['id']})";
        $output .= " - {$event['awayTeam']['name']} ({$event['awayTeam']['id']})";
        $output .= "<br> " . implode(":", $event['result']['sets']);
        $output .= "(" . implode(", ", $games) . ")";

        return $output;
    }
}