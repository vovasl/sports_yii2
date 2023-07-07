<?php


namespace common\helpers;

use frontend\models\sport\Odd;

class OddHelper
{

    /**
     * @param array $odds
     * @param string|null $type
     * @return array
     */
    public static function getStats(array $odds, string $type = null): array
    {
        $stats = [];
        foreach ($odds as $odd) {

            /** @var Odd $odd */
            if(!is_null($type) && $odd->add_type != $type) continue;

            $key = self::_getStatsKey($odd, self::totalSettings());
            $stats[$key]['count']++;
            $stats[$key]['profit'] += (int)$odd->profit;
            $stats[$key]['event'] = $odd->event;
        }

        ksort($stats);

        return $stats;
    }

    /**
     * @param Odd $odd
     * @param array $settings
     * @return int
     */
    public static function _getStatsKey(Odd $odd, array $settings): int
    {
        $val = 999;
        foreach ($settings as $k => $setting) {
            $val = $setting;

            /** for first element **/
            if($k == 0 && $odd->odd >= $setting) break;

            if($odd->odd < $settings[$k - 1] && $odd->odd >= $setting) break;
        }

        return $val;
    }

    /**
     * @return int[]
     */
    public static function totalSettings(): array
    {
        return [210, 200, 190, 180, 170, 0];
    }

    /**
     * @param $odd
     * @param $settings
     * @return string
     */
    public static function getStatsTitle($odd, $settings): string
    {
        $oddKey = array_search($odd, $settings);

        $prefix = '';
        if($oddKey == array_key_first($settings)) {
            $prefix = '>=';
            $val[] = $odd;
        }
        else if($oddKey == array_key_last($settings)) {
            $prefix = '<';
            $val[] = $settings[$oddKey - 1];
        }
        else {
            $val[] = $odd;
            $val[] = $settings[$oddKey - 1];
        }

        array_walk($val, function (&$val) {
            $val = round($val / 100, 2);
        });

        return $prefix . implode("-", $val);
    }

}