<?php


namespace common\helpers;

use frontend\models\sport\Odd;

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
    public static function getOddStats(array $odds): string
    {
        $count = $i = 0;
        foreach($odds as $odd) {
            if(is_null($odd->profit)) continue;
            if($odd->profit > 0) $i++;
            $count++;
        }

        return $count > 0 ? "{$i}/{$count}" : "";
    }

}