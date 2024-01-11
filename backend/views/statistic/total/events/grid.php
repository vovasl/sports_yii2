<?php

use backend\models\statistic\total\EventTotalSearch;
use common\helpers\EventHelper;
use frontend\models\sport\Event;
use frontend\models\sport\Round;
use frontend\models\sport\Surface;
use frontend\models\sport\Tour;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\web\View;
use yii\data\ActiveDataProvider;
use yii\widgets\LinkPager;
use yii\grid\ActionColumn;

/**
 * @var View $this
 * @var EventTotalSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 */

?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'summary' => 'Total: {totalCount}',
    'pager' => [
        'linkContainerOptions' => [
            'class' => 'page-item'
        ],
        'linkOptions' => [
            'class' => 'page-link'
        ],
        'disabledListItemSubTagOptions' => [
            'class' => 'page-link'
        ],
        'pagination' => $dataProvider->pagination,
        'class' => LinkPager::class
    ],
    'columns' => [
        [
            'label' => 'Start',
            'attribute' => 'start_at',
            'value' => 'formatStartAt',
            'filter' => '',
        ],
        [
            'label' => 'Tour',
            'attribute' => 'tour_id',
            'value' => 'eventTournament.tournamentTour.name',
            'filter' => Tour::dropdown(),
        ],
        [
            'label' => 'Surface',
            'attribute' => 'surface_id',
            'value' => 'eventTournament.tournamentSurface.name',
            'filter' => Surface::dropdown(),
        ],
        [
            'label' => 'Tournament',
            'attribute' => 'tournament_name',
            'value' => 'eventTournament.name'
        ],
        [
            'label' => 'Round',
            'attribute' => 'round_id',
            'value' => 'tournamentRound.name',
            'filter' => Round::dropdownFilter(),
        ],
        EventHelper::gridHomePlayer([
            'action' => '/statistic/total/players',
            'model' => 'PlayerTotalSearch',
            'player_field' => 'PlayerTotalSearch[player_name]',
            'search_data' => [
                'PlayerTotalSearch[tour]' => $searchModel->tour_id,
                'PlayerTotalSearch[surface]' => $searchModel->surface_id,
                'PlayerTotalSearch[round]' => !empty($searchModel->round_id) ? $searchModel->round_id : 100,
                'PlayerTotalSearch[min_moneyline]' => $searchModel->moneyline,
                'PlayerTotalSearch[five_sets]' => $searchModel->five_sets,
                'PlayerTotalSearch[add_type]' => 'over',
            ],
        ]),
        EventHelper::gridAwayPlayer([
            'action' => '/statistic/total/players',
            'model' => 'PlayerTotalSearch',
            'player_field' => 'PlayerTotalSearch[player_name]',
            'search_data' => [
                'PlayerTotalSearch[tour]' => $searchModel->tour_id,
                'PlayerTotalSearch[surface]' => $searchModel->surface_id,
                'PlayerTotalSearch[round]' => !empty($searchModel->round_id) ? $searchModel->round_id : 100,
                'PlayerTotalSearch[min_moneyline]' => $searchModel->moneyline,
                'PlayerTotalSearch[five_sets]' => $searchModel->five_sets,
                'PlayerTotalSearch[add_type]' => 'over',
            ],
        ]),
        EventHelper::gridHomeMoneyline(),
        EventHelper::gridAwayMoneyline(),
        [
            'attribute' => 'result',
            'label' => 'Result',
            'value' => 'result',
            'filter' => EventHelper::resultDropdown(),
        ],
        [
            'label' => '5 Sets',
            'attribute' => 'five_sets',
            'value' => function (Event $model) {
                return ($model->five_sets) ? 'Yes' : '';
            },
            'filter' => ['No', 'Yes'],
        ],
        [
            'attribute' => 'total_avg_value',
            'label' => 'Total',
            'value' => 'total_avg_value'
        ],
        [
            'attribute' => 'total',
            'label' => 'Sets',
            'value' => 'total',
            'filter' => EventHelper::setsDropdown(),
        ],
        [
            'attribute' => 'total_games',
            'label' => 'Games',
            'value' => 'total_games',
        ],
        [
            'label' => 'Over',
            'attribute' => 'total_over_min_profit',
            'value' => function (Event $model) {
                return EventHelper::getOddStat($model->totalsOver);
            },
            'filter' => [
                1 => 'Yes',
                0 => '0',
                -1 => 'No',
            ],
        ],
        [
            'label' => 'Odds',
            'attribute' => 'count_odds',
            'value' => 'count_odds',
            'filter' => [
                1 => 'Yes',
                -1 => 'No',
            ]
        ],
        /*            [
                        'label' => 'Over',
                        'value' => function(Event $model) use ($searchModel) {
                            return TotalHelper::getEventPlayersGeneralStat($model, Odd::ADD_TYPE['over'], $searchModel);
                        }
                    ],*/
        [
            'class' => ActionColumn::class,
            'template' => '{view}',
            'buttons' => [
                'view' => function ($url, $model) {
                    $options = [
                        'title' => 'Event',
                        'target' => '_blank'
                    ];
                    $url = Url::to(['event/view', 'id' => $model->id]);
                    return Html::a('<i class="fas fa-eye"></i>', $url, $options);
                },
            ],
        ],
    ],
]);

?>