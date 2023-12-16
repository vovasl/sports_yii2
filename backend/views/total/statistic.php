<?php

use backend\models\total\StatisticTotalSearch;
use frontend\models\sport\Odd;
use frontend\models\sport\Surface;
use frontend\models\sport\Total;
use yii\web\View;
use yii\data\ActiveDataProvider;
use frontend\models\sport\Tour;
use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var View $this
 * @var StatisticTotalSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 */

$this->title = 'Statistic';

$reset = "/total/statistic";

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
        'columns' => [
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
                'label' => '<1.76',
                'attribute' => 'percent_profit_0',
                'filter' => '',
                'value' => function(Total $model) {
                    return "{$model->percentProfit0}({$model->count_profit_0})";
                },
            ],
            [
                'label' => '1.76-1.85',
                'attribute' => 'percent_profit_1',
                'filter' => '',
                'value' => function(Total $model) {
                    return "{$model->percentProfit1}({$model->count_profit_1})";
                },
            ],
            [
                'label' => '1.86-1.96',
                'attribute' => 'percent_profit_2',
                'filter' => '',
                'value' => function(Total $model) {
                    return "{$model->percentProfit2}({$model->count_profit_2})";
                },
            ],
            [
                'label' => '1.97-2.08',
                'attribute' => 'percent_profit_3',
                'filter' => '',
                'value' => function(Total $model) {
                    return "{$model->percentProfit3}({$model->count_profit_3})";
                },
            ],
            [
                'label' => '>=2.09',
                'attribute' => 'percent_profit_4',
                'filter' => '',
                'value' => function(Total $model) {
                    return "{$model->percentProfit4}({$model->count_profit_4})";
                },
            ],
        ],
    ]); ?>

</div>
