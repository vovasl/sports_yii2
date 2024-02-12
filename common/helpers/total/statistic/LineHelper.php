<?php

namespace common\helpers\total\statistic;

use frontend\models\sport\Statistic;

class LineHelper
{

    /**
     * @param array $tour
     * @param array $surface
     * @param string $type
     * @param int $round
     * @param int $moneyline
     * @param array $totals
     * @param int $fiveSets
     * @return array
     */
    public static function getData(array $tour, array $surface, string $type, int $round, int $moneyline, array $totals, int $fiveSets): array
    {
        $data = [];
        foreach ($totals as $total) {

            $baseTotal = $total;

            /** get start total */
            $total--;

            $line = [];
            for ($i = 0; $i < 5; $i++) {

                $stat = Statistic::find()
                    ->select([
                        "round(count(profit_{$i})/2) count_events",
                        "round(sum(profit_{$i})/count(profit_{$i}), 1) percent_profit"
                    ])
                    ->joinWith([
                        "player",
                        "event",
                        "event.eventTournament",
                        "event.eventTournament.tournamentTour",
                        "event.eventTournament.tournamentSurface",
                        "odd{$i}"
                    ])
                    ->where(['tn_statistic.add_type' => $type])
                    ->andWhere(['!=', 'tn_event.round', $round])
                    ->andWhere(['>=', 'min_moneyline', $moneyline])
                    ->andWhere(['tn_event.five_sets' => $fiveSets])
                    ->andWhere(['IN', 'tn_tour.id', $tour])
                    ->andWhere(['IN', 'tn_surface.id', $surface])
                    ->andWhere(['sp_odd.value' => $total])
                    ->one()
                ;

                $line["{$total}"] = "{$stat->percentProfitOutput}";
                $total = $total + 0.5;
            }

            $data["{$baseTotal}"] = $line;
        }

        return $data;
    }
}