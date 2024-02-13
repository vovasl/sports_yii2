<?php

namespace common\helpers\statistic;

use common\helpers\TotalHelper;
use frontend\models\sport\Statistic;
use yii\db\ActiveRecord;

class TotalLineHelper
{

    /**
     * @param array $tour
     * @param array $surface
     * @param string $type
     * @param int $round
     * @param array $totals
     * @param int $fiveSets
     * @param int $favorite
     * @return array
     */
    public static function getLines(array $tour, array $surface, string $type, int $round, int $favorite, int $fiveSets, array $totals): array
    {
        $data = [];
        foreach ($totals as $total) {
            $data["{$total}"] = self::getLine($tour, $surface, $round, $type, $favorite, $fiveSets, $total);
        }
        return $data;
    }

    /**
     * @param array $tour
     * @param array $surface
     * @param string $type
     * @param int $round
     * @param int $fiveSets
     * @param $total
     * @param int $favorite
     * @return array
     */
    public static function getLine(array $tour, array $surface, int $round, string $type, int $favorite, int $fiveSets, $total): array
    {

        /** get start total */
        $total--;

        $data = [];
        for ($i = 0; $i < 5; $i++) {

            /** get stats */
            $stat = self::getStatistic($tour, $surface, $round, $type, $favorite, $fiveSets, $total, $i);

            $data["{$total}"] = "{$stat->percentProfitOutput}";
            $total += 0.5;
        }

        return $data;
    }

    /**
     * @param array $tour
     * @param array $surface
     * @param string $type
     * @param int $round
     * @param int $fiveSets
     * @param $total
     * @param int $oddNumber
     * @param int $favorite
     * @return array|ActiveRecord|null
     */
    public static function getStatistic(array $tour, array $surface, int $round, string $type, int $favorite, int $fiveSets, $total, int $oddNumber)
    {
        $model = Statistic::find()
            ->select([
                "round(count(profit_{$oddNumber})/2) count_events",
                "round(sum(profit_{$oddNumber})/count(profit_{$oddNumber}), 1) percent_profit"
            ])
            ->joinWith([
                "player",
                "event",
                "event.eventTournament",
                "event.eventTournament.tournamentTour",
                "event.eventTournament.tournamentSurface",
                "odd{$oddNumber}"
            ])
            ->where(['tn_statistic.add_type' => $type])
            ->andWhere(['!=', 'tn_event.round', $round])
            ->andWhere(['tn_event.five_sets' => $fiveSets])
            ->andWhere(['IN', 'tn_tour.id', $tour])
            ->andWhere(['IN', 'tn_surface.id', $surface])
            ->andWhere(['sp_odd.value' => $total])
        ;

        if ($favorite) {
            $model->andWhere(['<', 'min_moneyline', TotalHelper::OVER_FAVORITE_MAX_MONEYLINE]);
        } else {
            $model->andWhere(['>=', 'min_moneyline', TotalHelper::OVER_MIN_MONEYLINE]);
        }

        return $model->one();
    }
}