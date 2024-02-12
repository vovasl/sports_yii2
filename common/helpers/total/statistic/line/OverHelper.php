<?php

namespace common\helpers\total\statistic\line;

use common\helpers\total\statistic\LineHelper;
use common\helpers\TotalHelper;
use frontend\models\sport\Odd;
use frontend\models\sport\Round;
use frontend\models\sport\Surface;
use frontend\models\sport\Tour;

class OverHelper
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
                'title' => 'ATP Hard + Indoor - Main',
                'data' => self::ATPHardMain($fiveSets)
            ],
            [
                'title' => 'Challenger Clay - Main',
                'data' => self::ChallengerClayMain($fiveSets)
            ],
            [
                'title' => 'Challenger Indoor - Main',
                'data' => self::ChallengerIndoorMain($fiveSets)
            ],
            [
                'title' => 'Challenger Hard - Main',
                'data' => self::ChallengerHardMain($fiveSets)
            ]
        ];
    }

    /**
     * @param int $fiveSets
     * @return array
     */
    public static function ATPHardMain(int $fiveSets): array
    {
        return LineHelper::getData(
            Tour::ATP_ALL,
            [Surface::SURFACES['hard'], Surface::SURFACES['indoor']],
            Odd::ADD_TYPE['over'],
            Round::QUALIFIER,
            TotalHelper::OVER_MIN_MONEYLINE,
            self::TOTALS['atp']['hard'],
            $fiveSets
        );
    }

    /**
     * @param int $fiveSets
     * @return array
     */
    public static function ChallengerClayMain(int $fiveSets): array
    {
        return LineHelper::getData(
            [Tour::CHALLENGER],
            [Surface::SURFACES['clay']],
            Odd::ADD_TYPE['over'],
            Round::QUALIFIER,
            TotalHelper::OVER_MIN_MONEYLINE,
            self::TOTALS['challenger']['clay'],
            $fiveSets
        );
    }

    /**
     * @param int $fiveSets
     * @return array
     */
    public static function ChallengerHardMain(int $fiveSets): array
    {
        return LineHelper::getData(
            [Tour::CHALLENGER],
            [Surface::SURFACES['hard']],
            Odd::ADD_TYPE['over'],
            Round::QUALIFIER,
            TotalHelper::OVER_MIN_MONEYLINE,
            self::TOTALS['challenger']['hard'],
            $fiveSets
        );
    }

    /**
     * @param int $fiveSets
     * @return array
     */
    public static function ChallengerIndoorMain(int $fiveSets): array
    {
        return LineHelper::getData(
            [Tour::CHALLENGER],
            [Surface::SURFACES['indoor']],
            Odd::ADD_TYPE['over'],
            Round::QUALIFIER,
            TotalHelper::OVER_MIN_MONEYLINE,
            self::TOTALS['challenger']['indoor'],
            $fiveSets
        );
    }

}