<?php

use backend\models\statistic\total\PlayerTotalSearch;
use common\helpers\statistic\BaseHelper;
use common\helpers\TotalHelper;
use frontend\models\sport\Odd;
use frontend\models\sport\Round;
use frontend\models\sport\Surface;
use frontend\models\sport\Statistic;
use frontend\models\sport\Tour;
use yii\web\View;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;

/**
 * @var View $this
 * @var PlayerTotalSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 */

$this->title = 'Players - Total Over';

$reset = "/statistic/total/players-over";

?>

<div class="tournament-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Clear', [$reset], ['class' => 'btn btn-primary']) ?>
    </p>

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
                'label' => 'Player',
                'attribute' => 'player_name',
                'format' => 'raw',
                'value' => function(Statistic $model) use($searchModel) {
                    return Html::a($model->player->name, [
                        '/statistic/total/events-total',
                        'type' => Odd::ADD_TYPE['over'],
                        'EventTotalSearch[count_odds]' => 1,
                        'EventTotalSearch[result]' => 1,
                        'EventTotalSearch[moneyline]' => $searchModel->min_moneyline,
                        'EventTotalSearch[player]' => $model->player->name,
                        'EventTotalSearch[tour_id]' => $searchModel->tour,
                        'EventTotalSearch[surface_id]' => $searchModel->surface,
                        'EventTotalSearch[round_id]' => $searchModel->round,
                        'EventTotalSearch[favorite]' => $searchModel->favorite,
                        'EventTotalSearch[five_sets]' => $searchModel->five_sets,
                    ], [
                        'target'=>'_blank',
                    ]);
                }
            ],
            [
                'label' => 'Tour',
                'attribute' => 'tour',
                'value' => '',
                'filter' => Tour::dropdown()
            ],
            [
                'label' => 'Surface',
                'attribute' => 'surface',
                'value' => '',
                'filter' => Surface::dropdown()
            ],
            [
                'label' => 'Round',
                'attribute' => 'round',
                'value' => '',
                'filter' => Round::dropdownFilter(),
            ],
            'count_events',
            [
                'label' => 'Moneyline',
                'attribute' => 'min_moneyline',
                'value' => '',
            ],
            [
                'label' => 'Favorite',
                'attribute' => 'favorite',
                'value' => function(Statistic $model) use($searchModel) {
                    return $searchModel->favorite;
                },
                'filter' => [
                    'Yes' => 'Yes',
                    'No' => 'No'
                ],
            ],
            [
                'label' => '5 Sets',
                'attribute' => 'five_sets',
                'value' => '',
                'filter' => ['No', 'Yes'],
            ],
            [
                'label' => 'Type',
                'attribute' => 'add_type',
                'value' => 'add_type',
                'filter' => [
                    Odd::ADD_TYPE['over'] => Odd::ADD_TYPE['over'],
                    Odd::ADD_TYPE['under'] => Odd::ADD_TYPE['under']
                ]
            ],
            [
                'label' => BaseHelper::getStatsTitle(TotalHelper::ODDS, 0),
                'attribute' => 'percent_profit_0',
                'value' => 'percentProfit0',
                'filter' => '',
            ],
            [
                'label' => BaseHelper::getStatsTitle(TotalHelper::ODDS, 1),
                'attribute' => 'percent_profit_1',
                'value' => 'percentProfit1',
                'filter' => '',
            ],
            [
                'label' => BaseHelper::getStatsTitle(TotalHelper::ODDS, 2),
                'attribute' => 'percent_profit_2',
                'value' => 'percentProfit2',
                'filter' => '',
            ],
            [
                'label' => BaseHelper::getStatsTitle(TotalHelper::ODDS, 3),
                'attribute' => 'percent_profit_3',
                'value' => 'percentProfit3',
                'filter' => '',
            ],
            [
                'label' => BaseHelper::getStatsTitle(TotalHelper::ODDS, 4),
                'attribute' => 'percent_profit_4',
                'value' => 'percentProfit4',
                'filter' => '',
            ],
        ],
    ]); ?>

</div>
