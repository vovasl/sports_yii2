<?php

namespace common\helpers\statistic;

class BaseHelper
{

    /**
     * @param $settings
     * @param null $current
     * @return array|mixed
     */
    public static function getStatsTitle($settings, $current = null)
    {
        $odds = $settings;
        sort($odds);
        $data = [];

        foreach ($odds as $k => $odd) {
            $prefix = '';
            $titles = [];

            /** first title */
            if($odd == reset($odds)) {
                $prefix = '<';
                $titles[] = $odds[$k + 1];
            }
            /** last title */
            else if($odd == end($odds)) {
                $prefix = '>=';
                $titles[] = $odd;
            }
            /** middle title */
            else {
                $titles[] = $odd;
                $titles[] = $odds[$k + 1];
            }

            /** prepare titles for odd view */
            array_walk($titles, function (&$odd) {
                $odd = round($odd / 100, 2);
            });

            $data[] = $prefix . implode("-", $titles);

            /** get one title */
            if(!is_null($current) && $current == $k) return end($data);
        }

        return $data;
    }
    
}