<?php

namespace backend\helpers;

use frontend\models\sport\Odd;
use frontend\models\sport\PlayerTotal;
use backend\models\statistic\total\EventTotalSearch;

class IndexHelper
{

    /**
     * @return array[]
     */
    public static function getEventsSettings(): array
    {
        return [
            [
                'title' => 'Events - Total Over',
                'url' => [
                    'statistic/total/events-total',
                    'type' => Odd::ADD_TYPE['over']
                ],
                'type' => Odd::ADD_TYPE['over'],
                'add_type' => PlayerTotal::TYPE['over-favorite'],
                'search_model' => new EventTotalSearch(),
                'search_model_name' => 'EventTotalSearch',
            ],
            [
                'title' => 'Events - Total Under',
                'url' => [
                    'statistic/total/events-total',
                    'type' => Odd::ADD_TYPE['under']
                ],
                'type' => Odd::ADD_TYPE['under'],
                'add_type' => PlayerTotal::TYPE['under-favorite'],
                'search_model' => new EventTotalSearch(),
                'search_model_name' => 'EventTotalSearch',
            ]
        ];
    }
}