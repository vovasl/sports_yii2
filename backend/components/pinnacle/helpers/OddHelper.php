<?php

namespace backend\components\pinnacle\helpers;


use backend\components\pinnacle\Pinnacle;

class OddHelper
{

    /**
     * @param array $line
     * @return array
     */
    public static function prepareLine(array $line): array
    {
        $config = Pinnacle::TENNIS_ODDS_CONFIG;
        $odds = [];

        foreach ($line as $type => $periods) {
            if(!empty($periods['lineId'])) $periods = [$periods];
            foreach ($periods as $period) {
                foreach ($config[$type] as $configLine) {
                    if (isset($period[$configLine])) {
                        $odds[$type][$configLine] = $period[$configLine];
                    }
                }
            }
        }

        return $odds;
    }

}