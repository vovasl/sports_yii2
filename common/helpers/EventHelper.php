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
     * @param array $uri
     * @return array
     */
    public static function gridHomePlayer(array $uri = []): array
    {
        return [
            'label' => 'Event',
            'attribute' => 'player',
            'format' => 'raw',
            'value' => function (Event $model) use($uri)  {
                return $model->outputPlayer('homePlayer', $uri);
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
     * @param array $uri
     * @return array
     */
    public static function gridAwayPlayer(array $uri = []): array
    {
        return [
            'format' => 'raw',
            'value' => function(Event $model) use($uri) {
                return $model->outputPlayer('awayPlayer', $uri);
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
     * @return array
     */
    public static function gridHomeMoneyline(): array
    {
        return [
            'label' => 'Moneyline',
            'attribute' => 'moneyline',
            'value' => 'homeMoneylineOddVal',
            'format' => 'raw',
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
    public static function gridAwayMoneyline(): array
    {
        return [
            'label' => 'Away',
            'value' =>'awayMoneylineOddVal',
            'format' => 'raw',
            'headerOptions' => [
                'style' => 'display: none;',
            ],
            'filterOptions' => [
                'style' => 'display: none;',
            ]
        ];
    }

    public static function parseValueFilter($val)
    {
        $pattern = '#(\d.*)(<.*|>.*)#';
        preg_match($pattern, $val, $data);

        return $data;
    }

}