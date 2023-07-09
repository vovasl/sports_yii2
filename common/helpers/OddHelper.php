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
            $stats[$key]['events'][] = $odd->event . '||' . $odd->id;
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
     * @param $settings
     * @return array
     */
    public static function getStatsTitle($settings): array
    {
        $odds = $settings;
        sort($odds);
        $data = [];

        foreach ($odds as $odd) {
            $prefix = '';
            $titles = [];
            $key = array_search($odd, $settings);

            /** last title */
            if($key == array_key_first($settings)) {
                $prefix = '>=';
                $titles[] = $odd;
            }
            /** first title */
            else if($key == array_key_last($settings)) {
                $prefix = '<';
                $titles[] = $settings[$key - 1];
            }
            /** middle title */
            else {
                $titles[] = $odd;
                $titles[] = $settings[$key - 1];
            }

            /** prepare titles for odd view */
            array_walk($titles, function (&$odd) {
                $odd = round($odd / 100, 2);
            });

            $data[] = $prefix . implode("-", $titles);
        }

        return $data;
    }

    /**
     * @param array $tournaments
     * @return array
     */
    public static function tournamentsStats(array $tournaments): array
    {
        $stats = [];
        foreach (Odd::ADD_TYPE as $type) {
            $all = [];
            foreach ($tournaments as $tournament) {
                $tournamentStats = self::getStats(TournamentHelper::getEventsOdds($tournament), $type);
                $stats[$type][$tournament->id] = [
                    'name' => $tournament->name,
                    'stats' => $tournamentStats
                ];
                $all = self::generalStats($tournamentStats, $all);
            }
            $stats[$type]['all'] = [
                'name' => 'ALL',
                'stats' => $all
            ];
        }

        return $stats;
    }

    /**
     * @param array $localStats
     * @param $stats
     * @return array
     */
    public static function generalStats(array $localStats, $stats): array
    {
        foreach ($localStats as $k => $odd) {
            $stats[$k]['count'] += $odd['count'];
            $stats[$k]['profit'] += $odd['profit'];
        }

        return $stats;
    }

}