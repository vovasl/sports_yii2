<?php


namespace backend\helpers\total;

use backend\models\statistic\FilterModel;
use common\helpers\EventFilterHelper;

class OverHelper
{

    /**
     * @return array
     */
    public static function ATPHard(): array
    {
        return [
            'tour' => 1,
            'surface' => 2,
            'rounds' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 11],
            'value' => 21.5,
            'odds' => [
                'min' => 165,
                'max' => 300
            ],
            'moneyline' => [
                'filter' => EventFilterHelper::MONEYLINE_FILTER['more'],
                'limit' => 150
            ]
        ];
    }

    /**
     * @return array
     */
    public static function ATPIndoor(): array
    {
        return [
            'tour' => 1,
            'surface' => 4,
            'rounds' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 11],
            'value' => 21.5,
            'odds' => [
                'min' => 165,
                'max' => 300
            ],
            'moneyline' => [
                'filter' => EventFilterHelper::MONEYLINE_FILTER['more'],
                'limit' => 150
            ]
        ];
    }

    /**
     * @return array
     */
    public static function challengerClay(): array
    {
        return [
            'tour' => 2,
            'surface' => 1,
            'rounds' => [1, 2, 3, 4, 6, 7, 8, 9, 11],
            'value' => 20.5,
            'odds' => [
                'min' => 165,
                'max' => 300
            ],
            'moneyline' => [
                'filter' => EventFilterHelper::MONEYLINE_FILTER['more'],
                'limit' => 150
            ]
        ];
    }

    /**
     * @return array
     */
    public static function challengerHard(): array
    {
        return [
            'tour' => 2,
            'surface' => 2,
            'rounds' => [1, 2, 3, 4, 6, 7, 8, 9, 11],
            'value' => 21.5,
            'odds' => [
                'min' => 165,
                'max' => 300
            ],
            'moneyline' => [
                'filter' => EventFilterHelper::MONEYLINE_FILTER['more'],
                'limit' => 150
            ]
        ];
    }

    /**
     * @return array
     */
    public static function challengerIndoor(): array
    {
        return [
            'tour' => 2,
            'surface' => 4,
            'rounds' => [1, 2, 3, 4, 6, 7, 8, 9, 11],
            'value' => 21.5,
            'odds' => [
                'min' => 165,
                'max' => 300
            ],
            'moneyline' => [
                'filter' => EventFilterHelper::MONEYLINE_FILTER['more'],
                'limit' => 150
            ]
        ];
    }

}