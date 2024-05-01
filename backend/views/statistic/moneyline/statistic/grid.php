<?php

use backend\models\statistic\moneyline\StatisticSearch;
use common\helpers\statistic\BaseHelper;
use common\helpers\statistic\MoneylineHelper;
use common\helpers\TotalHelper;
use frontend\models\sport\Odd;
use frontend\models\sport\Round;
use frontend\models\sport\Surface;
use yii\web\View;
use yii\data\ActiveDataProvider;
use frontend\models\sport\Tour;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/**
 * @var View $this
 * @var StatisticSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 */

?>

<div class="tournament-index">

    <p>
        <?= Html::a('Clear', Url::canonical(), ['class' => 'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => 'Total: {totalCount}',
        'columns' => [
            'date_from',
            'date_to',
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
                'label' => 'Tournament',
                'attribute' => 'tournament',
                'value' => '',
            ],
            [
                'label' => 'Round',
                'attribute' => 'round',
                'value' => '',
                'filter' => Round::dropdownFilter(),
            ],
            [
                'label' => '5 Sets',
                'attribute' => 'five_sets',
                'value' => '',
                'filter' => ['No', 'Yes'],
            ],
            [
                'label' => BaseHelper::getStatsTitle(MoneylineHelper::ODDS, 0),
                'attribute' => 'percent_profit_0',
                'filter' => '',
                'value' => 'percentProfit0',
            ],
            [
                'label' => BaseHelper::getStatsTitle(MoneylineHelper::ODDS, 1),
                'attribute' => 'percent_profit_1',
                'filter' => '',
                'value' => 'percentProfit1',
            ],
            [
                'label' => BaseHelper::getStatsTitle(MoneylineHelper::ODDS, 2),
                'attribute' => 'percent_profit_2',
                'filter' => '',
                'value' => 'percentProfit2',
            ],
            [
                'label' => BaseHelper::getStatsTitle(MoneylineHelper::ODDS, 3),
                'attribute' => 'percent_profit_3',
                'filter' => '',
                'value' => 'percentProfit3',
            ],
            [
                'label' => BaseHelper::getStatsTitle(MoneylineHelper::ODDS, 4),
                'attribute' => 'percent_profit_4',
                'filter' => '',
                'value' => 'percentProfit4',
            ],
        ],
    ]); ?>

</div>
