<?php

namespace common\helpers\statistic;

use common\helpers\TotalHelper;
use frontend\models\sport\Odd;
use frontend\models\sport\Round;
use frontend\models\sport\Statistic;
use frontend\models\sport\Surface;
use frontend\models\sport\Tour;

class TotalLineHelper
{

    CONST TOTALS = [
        'atp' => [
            'hard' => [22, 22.5, 23, 23.5]
        ],
        'challenger' => [
            'clay' => [21, 21.5, 22, 22.5],
            'hard' => [21.5, 22, 22.5, 23],
            'indoor' => [21.5, 22, 22.5, 23]
        ],
    ];

    /**
     * @param int $fiveSets
     * @return array[]
     */
    public static function getItems(int $fiveSets = 0): array
    {
        return [
            [
                'title' => 'ATP Hard + Indoor - Main - OVER',
                'data' => self::ATPHardMain(Odd::ADD_TYPE['over'], $fiveSets)
            ],
            [
                'title' => 'Challenger Clay - Main - OVER',
                'data' => self::ChallengerClayMain(Odd::ADD_TYPE['over'], $fiveSets)
            ],
            [
                'title' => 'Challenger Indoor - Main - OVER',
                'data' => self::ChallengerIndoorMain(Odd::ADD_TYPE['over'], $fiveSets)
            ],
            [
                'title' => 'Challenger Hard - Main - OVER',
                'data' => self::ChallengerHardMain(Odd::ADD_TYPE['over'], $fiveSets)
            ],
        ];
    }

    /**
     * @param string $type
     * @param int $fiveSets
     * @return array
     */
    public static function ATPHardMain(string $type, int $fiveSets): array
    {
        return self::getData(
            Tour::ATP_ALL,
            [Surface::SURFACES['hard'], Surface::SURFACES['indoor']],
            $type,
            Round::QUALIFIER,
            TotalHelper::OVER_MIN_MONEYLINE,
            self::TOTALS['atp']['hard'],
            $fiveSets
        );
    }

    /**
     * @param string $type
     * @param int $fiveSets
     * @return array
     */
    public static function ChallengerClayMain(string $type, int $fiveSets): array
    {
        return self::getData(
            [Tour::CHALLENGER],
            [Surface::SURFACES['clay']],
            $type,
            Round::QUALIFIER,
            TotalHelper::OVER_MIN_MONEYLINE,
            self::TOTALS['challenger']['clay'],
            $fiveSets
        );
    }

    /**
     * @param string $type
     * @param int $fiveSets
     * @return array
     */
    public static function ChallengerHardMain(string $type, int $fiveSets): array
    {
        return self::getData(
            [Tour::CHALLENGER],
            [Surface::SURFACES['hard']],
            $type,
            Round::QUALIFIER,
            TotalHelper::OVER_MIN_MONEYLINE,
            self::TOTALS['challenger']['hard'],
            $fiveSets
        );
    }

    /**
     * @param string $type
     * @param int $fiveSets
     * @return array
     */
    public static function ChallengerIndoorMain(string $type, int $fiveSets): array
    {
        return self::getData(
            [Tour::CHALLENGER],
            [Surface::SURFACES['indoor']],
            $type,
            Round::QUALIFIER,
            TotalHelper::OVER_MIN_MONEYLINE,
            self::TOTALS['challenger']['indoor'],
            $fiveSets
        );
    }


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