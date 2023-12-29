<?php


use backend\models\EventSearch;
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
 * @var EventSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 */

$this->title = 'Events';

$this->params['breadcrumbs'][] = ['label' => 'Tournaments', 'url' => ['/tournament']];
$this->params['breadcrumbs'][] = 'Events';

$reset = "/event";

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
            EventHelper::gridHomePlayer(),
            EventHelper::gridAwayPlayer(),
            [
                'attribute' => 'result',
                'label' => 'Result',
                'value' =>'result',
                'filter' => EventHelper::resultDropdown(),
            ],
            EventHelper::gridHomeMoneyline(),
            EventHelper::gridAwayMoneyline(),
            [
                'label' => 'Odds',
                'attribute' => 'count_odds',
                //'value' => 'count_odds',
                'value' => function($model) {
                    return count($model->odds);
                },
                'filter' => [
                    1 => 'Yes',
                    -1 => 'No',
                    -2 => 'No(Finished)'
                ]
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{view} {update} {delete} {add-line}',
                'visibleButtons' => [
                    'delete' => function (Event $model, $key, $index) {
                        return $model->actionDelete();
                    },
                    'update' => function (Event $model, $key, $index) {
                        return $model->actionUpdate();
                    },
                    'add-line' => function(Event $model, $key, $index) {
                        return $model->actionAddLine();
                    }
                ],
                'buttons' => [
                    'view' => function ($url, $model) {
                        $options = [
                            'title' => 'Event',
                            'target' => '_blank'
                        ];
                        $url = Url::to(['event/view', 'id' => $model->id]);
                        return Html::a('<i class="fas fa-eye"></i>', $url, $options);
                    },
                    'add-line' => function ($url, $model) {
                        $options = [
                            'title' => 'Add Line',
                            'target' => '_blank'
                        ];
                        $url = Url::to(['add-line-log', 'id' => $model->id]);
                        return Html::a('<i class="fas fa-edit"></i>', $url, $options);
                    },
                ],

            ],
        ],
    ]); ?>

</div>
