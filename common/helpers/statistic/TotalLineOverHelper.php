<?php

namespace common\helpers\statistic;

use frontend\models\sport\Odd;
use frontend\models\sport\Round;
use frontend\models\sport\Surface;
use frontend\models\sport\Tour;

class TotalLineOverHelper
{

    CONST TOTALS = [
        'atp' => [
            'clay' => [21.5, 22, 22.5, 23],
            'hard' => [22, 22.5, 23, 23.5]
        ],
        'challenger' => [
            'clay' => [21, 21.5, 22, 22.5],
            'hard' => [21.5, 22, 22.5, 23],
            'indoor' => [21.5, 22, 22.5, 23]
        ],
        'wta' => [
            'hard' => [21, 21.5, 22]
        ],
    ];

    CONST TOTALS_FAVORITE = [
        'atp' => [
            'clay' => [],
            'hard' => [20, 20.5, 21, 21.5, 22, 22.5, 23]
        ],
        'challenger' => [
            'clay' => [20, 20.5, 21, 21.5],
            'hard' => [19.5, 20, 20.5, 21, 21.5, 22],
            'indoor' => []
        ],
        'wta' => [
            'hard' => [18.5, 19, 19.5, 20, 20.5, 21]
        ],
    ];

    /**
     * @param int $favorite
     * @param int $fiveSets
     * @return array[]
     */
    public static function getItems(int $favorite = 0, int $fiveSets = 0): array
    {
        $totals = ($favorite) ? self::TOTALS_FAVORITE : self::TOTALS;

        return [
            [
                'title' => 'ATP Hard + Indoor - Main',
                'data' => self::ATPHard(Odd::ADD_TYPE['over'], $favorite, $fiveSets, $totals)
            ],
            [
                'title' => 'ATP Clay - Main',
                'data' => self::ATPClay(Odd::ADD_TYPE['over'], $favorite, $fiveSets, $totals)
            ],
            [
                'title' => 'WTA Hard - Main',
                'data' => self::WTAHard(Odd::ADD_TYPE['over'], $favorite, $fiveSets, $totals)
            ],
            [
                'title' => 'Challenger Clay - Main',
                'data' => self::ChallengerClay(Odd::ADD_TYPE['over'], $favorite, $fiveSets, $totals)
            ],
            [
                'title' => 'Challenger Indoor - Main',
                'data' => self::ChallengerIndoor(Odd::ADD_TYPE['over'], $favorite, $fiveSets, $totals)
            ],
            [
                'title' => 'Challenger Hard - Main',
                'data' => self::ChallengerHard(Odd::ADD_TYPE['over'], $favorite, $fiveSets, $totals)
            ],
        ];
    }

    /**
     * @param string $type
     * @param int $favorite
     * @param int $fiveSets
     * @param array $totals
     * @return array
     */
    public static function ATPHard(string $type, int $favorite, int $fiveSets, array $totals): array
    {
        return TotalLineHelper::getLines(
            Tour::ATP_ALL,
            [Surface::SURFACES['hard'], Surface::SURFACES['indoor']],
            $type,
            Round::QUALIFIER,
            $favorite,
            $fiveSets,
            $totals['atp']['hard']
        );
    }

    /**
     * @param string $type
     * @param int $favorite
     * @param int $fiveSets
     * @param array $totals
     * @return array
     */
    public static function ATPClay(string $type, int $favorite, int $fiveSets, array $totals): array
    {
        return TotalLineHelper::getLines(
            Tour::ATP_ALL,
            [Surface::SURFACES['clay']],
            $type,
            Round::QUALIFIER,
            $favorite,
            $fiveSets,
            $totals['atp']['clay']
        );
    }

    /**
     * @param string $type
     * @param int $favorite
     * @param int $fiveSets
     * @param array $totals
     * @return array
     */
    public static function ChallengerClay(string $type, int $favorite, int $fiveSets, array $totals): array
    {
        return TotalLineHelper::getLines(
            [Tour::CHALLENGER],
            [Surface::SURFACES['clay']],
            $type,
            Round::QUALIFIER,
            $favorite,
            $fiveSets,
            $totals['challenger']['clay']
        );
    }

    /**
     * @param string $type
     * @param int $favorite
     * @param int $fiveSets
     * @param array $totals
     * @return array
     */
    public static function ChallengerHard(string $type, int $favorite, int $fiveSets, array $totals): array
    {
        return TotalLineHelper::getLines(
            [Tour::CHALLENGER],
            [Surface::SURFACES['hard']],
            $type,
            Round::QUALIFIER,
            $favorite,
            $fiveSets,
            $totals['challenger']['hard']
        );
    }

    /**
     * @param string $type
     * @param int $favorite
     * @param int $fiveSets
     * @param array $totals
     * @return array
     */
    public static function ChallengerIndoor(string $type, int $favorite, int $fiveSets, array $totals): array
    {
        return TotalLineHelper::getLines(
            [Tour::CHALLENGER],
            [Surface::SURFACES['indoor']],
            $type,
            Round::QUALIFIER,
            $favorite,
            $fiveSets,
            $totals['challenger']['indoor']
        );
    }

    /**
     * @param string $type
     * @param int $favorite
     * @param int $fiveSets
     * @param array $totals
     * @return array
     */
    public static function WTAHard(string $type, int $favorite, int $fiveSets, array $totals): array
    {
        return TotalLineHelper::getLines(
            Tour::WTA_ALL,
            [Surface::SURFACES['hard']],
            $type,
            Round::QUALIFIER,
            $favorite,
            $fiveSets,
            $totals['wta']['hard']
        );
    }

}