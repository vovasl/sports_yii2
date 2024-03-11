<?php

namespace common\helpers\statistic;

use common\helpers\TotalHelper;
use frontend\models\sport\Round;
use frontend\models\sport\Statistic;
use yii\db\ActiveQuery;
use yii\helpers\Url;

class TotalLineHelper
{

    /**
     * @param array $tour
     * @param array $surface
     * @param string $type
     * @param array $round
     * @param int $favorite
     * @param int $fiveSets
     * @param array $totals
     * @return array
     */
    public static function getLines(array $tour, array $surface, string $type, array $round, int $favorite, int $fiveSets, array $totals): array
    {
        /** search params */
        $params = [
            'tour' => $tour,
            'surface' => $surface,
            'round' => $round,
            'type' => $type,
            'favorite' => $favorite,
            'five_sets' => $fiveSets,
        ];

        $data = [];
        foreach ($totals as $total) {
            $data["{$total}"] = self::getLine($params, $total);
        }

        return $data;
    }

    /**
     * @param $total
     * @param array $params
     * @return array
     */
    public static function getLine(array $params, $total): array
    {

        /** get start total */
        $total--;

        $data = [];
        for ($i = 0; $i < 5; $i++) {

            $params['total'] = $total;
            $params['odd_number'] = $i;

            /** get stats */
            $stat = self::getStatistic($params);
            $modelStat = $stat->one();

            $data["{$total}"] = [
                'stat' => $modelStat->percentProfitOutput,
                'link' => Url::to([
                    'statistic/total/events',
                    'statistic-line' => json_encode($params),
                ])
            ];

            $total += 0.5;
        }

        return $data;
    }

    /**
     * @param array $params
     * @return ActiveQuery
     */
    public static function getStatistic(array $params): ActiveQuery
    {
        $oddNumber = $params['odd_number'];
        $model = Statistic::find()
            ->select([
                "round(count(profit_{$oddNumber})/2) count_events",
                "round(sum(profit_{$oddNumber})/count(profit_{$oddNumber}), 1) percent_profit",
            ])
            ->joinWith([
                "player",
                "event",
                "event.eventTournament",
                "event.eventTournament.tournamentTour",
                "event.eventTournament.tournamentSurface",
                "odd{$oddNumber}"
            ])
            ->where(['tn_statistic.add_type' => $params['type']])
            ->andWhere(['IN', 'tn_tour.id', $params['tour']])
            ->andWhere(['IN', 'tn_surface.id', $params['surface']])
            ->andWhere(['tn_event.five_sets' => $params['five_sets']])
            ->andWhere(['sp_odd.value' => $params['total']])
        ;

        /** round filter */
        if(in_array(Round::MAIN, $params['round'])) {
            $model->andWhere(['!=', 'tn_event.round', Round::QUALIFIER]);
        }
        else {
            $model->andWhere(['IN', 'tn_event.round', $params['round']]);
        }

        /** favorite filter */
        if ($params['favorite']) {
            $model->andWhere(['<', 'min_moneyline', TotalHelper::MONEYLINE['over']['favorite']['max']]);
        } else {
            $model->andWhere(['>=', 'min_moneyline', TotalHelper::MONEYLINE['over']['min']]);
        }

        return $model;
    }
}