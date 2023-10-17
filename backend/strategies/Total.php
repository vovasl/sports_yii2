<?php

namespace backend\strategies;

use backend\models\statistic\FilterModel;

class Total
{

    /**
     * @return array
     */
    public static function challengerClayOver(): array
    {
        return [
            'title' => 'Challenger Clay R1',
            'tour' => 2,
            'surface' => 1,
            'rounds' => [4],
            'values' => [21],
            'odds' => [
                'min' => 175,
                'max' => 185
            ],
            'moneyline' => [
                'filter' => FilterModel::FILTER['more'],
                'limit' => 140
            ]
        ];
    }

    /**
     * @return array
     */
    public static function ATPHardOver(): array
    {
        return [
            'title' => 'ATP Hard R1-R16',
            'tour' => 1,
            'surface' => 2,
            'rounds' => [1, 2, 4, 6],
            'values' => [21.5],
            'odds' => [
                'min' => 175,
                'max' => 185
            ],
            'moneyline' => [
                'filter' => FilterModel::FILTER['more'],
                'limit' => 140
            ]
        ];
    }

    /**
     * @return array
     */
    public static function ATPHardOverTest(): array
    {
        return [
            'title' => 'ATP Hard Test',
            'tour' => 1,
            'surface' => 2,
            'rounds' => [1, 2, 4, 6],
            'values' => [22.5],
            'odds' => [
                'min' => 195,
                'max' => 210
            ],
            'moneyline' => [
                'filter' => FilterModel::FILTER['more'],
                'limit' => 140
            ]
        ];
    }

    /**
     * @return array
     */
    public static function challengerClayOverTest(): array
    {
        return [
            'title' => 'Challenger Clay Test',
            'tour' => 2,
            'surface' => 1,
            'rounds' => [4],
            'values' => [21],
            'odds' => [
                'min' => 175,
                'max' => 185
            ],
            'moneyline' => [
                'filter' => FilterModel::FILTER['more'],
                'limit' => 140
            ]
        ];
    }

}