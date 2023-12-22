<?php

use backend\models\event\EventOddMoveSearch;
use frontend\models\sport\Event;
use frontend\models\sport\OddMove;
use yii\web\View;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use frontend\models\sport\Round;
use frontend\models\sport\Surface;
use frontend\models\sport\Tour;
use common\helpers\EventHelper;
use yii\grid\ActionColumn;
use yii\helpers\Url;

/**
 * @var View $this
 * @var EventOddMoveSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 */

$this->title = 'Events';

$this->params['breadcrumbs'][] = $this->title;

$reset = "/event/odd-move";

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
            EventHelper::gridHomePlayer(['model' => 'EventOddMoveSearch']),
            EventHelper::gridAwayPlayer(['model' => 'EventOddMoveSearch']),
            [
                'attribute' => 'home_moneyline_odd',
                'label' => 'Home',
                'value' => 'homeMoneylineOddVal'
            ],
            [
                'attribute' => 'away_moneyline_odd',
                'label' => 'Away',
                'value' =>'awayMoneylineOddVal',
                'filter' => '',
            ],
            [
                'attribute' => 'o_type_name',
                'label' => 'Type',
                'value' => 'o_type_name',
                'filter' => '',
            ],
            [
                'attribute' => 'odd_move_value',
                'label' => 'Move',
                'value' => 'odd_move_value'
            ],
            [
                'attribute' => 'odd_move_value_type',
                'label' => 'Move Type',
                'value' => 'oddMoveValueType',
                'filter' => OddMove::dropdownFilterValueType(),
            ],
            [
                'attribute' => 'odd_move_status',
                'label' => 'Status',
                'value' => 'oddMoveStatus',
                'filter' => OddMove::dropdownFilterStatuses(),
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
