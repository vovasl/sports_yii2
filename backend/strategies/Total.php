<?php

namespace backend\strategies;

use backend\models\statistic\FilterModel;
use common\helpers\EventFilterHelper;

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
            'value' => 21,
            'odds' => [
                'min' => 175,
                'max' => 185
            ],
            'moneyline' => [
                'filter' => EventFilterHelper::MONEYLINE_FILTER['more'],
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
            'value' => 21.5,
            'odds' => [
                'min' => 175,
                'max' => 185
            ],
            'moneyline' => [
                'filter' => EventFilterHelper::MONEYLINE_FILTER['more'],
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
            'value' => 22.5,
            'odds' => [
                'min' => 195,
                'max' => 210
            ],
            'moneyline' => [
                'filter' => EventFilterHelper::MONEYLINE_FILTER['more'],
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
            'value' => 21,
            'odds' => [
                'min' => 175,
                'max' => 185
            ],
            'moneyline' => [
                'filter' => EventFilterHelper::MONEYLINE_FILTER['more'],
                'limit' => 140
            ]
        ];
    }

}