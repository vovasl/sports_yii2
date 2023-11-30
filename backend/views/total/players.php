<?php


use backend\models\total\PlayerTotalSearch;
use frontend\models\sport\Odd;
use frontend\models\sport\Surface;
use frontend\models\sport\Total;
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

$this->title = 'Players';

$reset = "/total/players";

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
                'value' => function(Total $model) use($searchModel) {
                    return Html::a($model->player->name, [
                        'total/events',
                        'EventTotalSearch[player]' => $model->player->name,
                        'EventTotalSearch[count_odds]' => 1,
                        'EventTotalSearch[tour_id]' => $searchModel->tour_id,
                        'EventTotalSearch[surface_id]' => $searchModel->surface_id,
                        'EventTotalSearch[five_sets]' => $searchModel->five_sets,
                    ], [
                        'target'=>'_blank',
                    ]);
                }
            ],
            [
                'label' => 'Tour',
                'attribute' => 'tour_id',
                'value' => '',
                'filter' => Tour::dropdown()
            ],
            [
                'label' => 'Surface',
                'attribute' => 'surface_id',
                'value' => '',
                'filter' => Surface::dropdown()
            ],
            'count_events',
            [
                'label' => 'Type',
                'attribute' => 'type',
                'value' => 'type',
                'filter' => [
                    Odd::ADD_TYPE['over'] => Odd::ADD_TYPE['over'],
                    Odd::ADD_TYPE['under'] => Odd::ADD_TYPE['under']
                ]
            ],
            [
                'label' => 'Moneyline',
                'attribute' => 'min_moneyline',
                'value' => '',
            ],
            [
                'label' => '5 Sets',
                'attribute' => 'five_sets',
                'value' => '',
                'filter' => ['No', 'Yes'],
            ],
            [
                'label' => '<1.75',
                'attribute' => 'sum_profit_0',
                'value' => 'sum_profit_0',
                'filter' => '',
            ],
            [
                'label' => '1.75-1.85',
                'attribute' => 'sum_profit_1',
                'value' => 'sum_profit_1',
                'filter' => '',
            ],
            [
                'label' => '1.85-1.97',
                'attribute' => 'sum_profit_2',
                'value' => 'sum_profit_2',
                'filter' => '',
            ],
            [
                'label' => '1.97-2.1',
                'attribute' => 'sum_profit_3',
                'value' => 'sum_profit_3',
                'filter' => '',
            ],
            [
                'label' => '>=2.1',
                'attribute' => 'sum_profit_4',
                'value' => 'sum_profit_4',
                'filter' => '',
            ],
        ],
    ]); ?>

</div>
