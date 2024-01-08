<?php

use common\helpers\EventHelper;
use frontend\models\sport\Event;
use frontend\models\sport\Round;
use frontend\models\sport\Surface;
use frontend\models\sport\Tour;
use yii\web\View;
use backend\models\total\EventTotalOverSearch;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use yii\grid\ActionColumn;
use yii\helpers\Url;

/**
 * @var View $this
 * @var EventTotalOverSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 */

$this->title = 'Events Total Over';

$reset = "/total/events-total-over";

?>

<div class="tournament-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('events-total-over/stats', ['dataProvider' => $dataProvider]) ?>

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
            EventHelper::gridHomePlayer(),
            EventHelper::gridAwayPlayer(),
            [
                'attribute' => 'total_over_value',
                'label' => 'Total',
                'value' => 'total_over_value'
            ],
            [
                'attribute' => 'result',
                'label' => 'Result',
                'value' =>'result',
                'filter' => EventHelper::resultDropdown(),
            ],
            EventHelper::gridHomeMoneyline(),
            EventHelper::gridAwayMoneyline(),
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
                'filter' => '',
            ],
            [
                'label' => '5 Sets',
                'attribute' => 'five_sets',
                'value' => function(Event $model) {
                    return ($model->five_sets) ? 'Yes' : '';
                },
                'filter' => ['No', 'Yes'],
            ],
            [
                'label' => 'Over',
                'value' => function(Event $model) {
                    return EventHelper::getOddStat($model->totalsOver);
                }
            ],
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
    ]); ?>

</div>
