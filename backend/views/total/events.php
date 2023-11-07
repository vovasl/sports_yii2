<?php


use backend\models\total\EventTotalSearch;
use common\helpers\EventHelper;
use common\helpers\OddHelper;
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

$this->title = 'Events';

$this->params['breadcrumbs'][] = ['label' => 'Tournaments', 'url' => ['/tournament']];
$this->params['breadcrumbs'][] = 'Events';

$reset = "/total/events";

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
            EventHelper::gridHomePlayer('total/events', 'EventTotalSearch'),
            EventHelper::gridAwayPlayer('total/events', 'EventTotalSearch'),
            [
                'attribute' => 'result',
                'label' => 'Result',
                'value' =>'result',
                'filter' => EventHelper::resultDropdown(),
            ],
            [
                'attribute' => 'total_over_value',
                'label' => 'Total',
                'value' => 'total_over_value'
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
                'filter' => '',
            ],
            [
                'label' => 'Over',
                'value' => function(Event $model) {
                    return EventHelper::getOddStat($model->totalsOver);
                }
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
