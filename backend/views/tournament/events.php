<?php


use frontend\models\sport\Event;
use frontend\models\sport\Round;
use frontend\models\sport\Surface;
use frontend\models\sport\Tour;
use frontend\models\sport\Tournament;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\View;
use backend\models\TournamentEventSearch;
use yii\data\ActiveDataProvider;
use yii\widgets\LinkPager;

/**
 * @var View $this
 * @var Tournament $model
 * @var TournamentEventSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 */

$this->title = $model->name;

$this->params['breadcrumbs'][] = ['label' => 'Tournaments', 'url' => ['/tournament']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ["/tournament/{$model->id}"]];
$this->params['breadcrumbs'][] = 'Events';

$reset = "/tournament/{$model->id}/event";

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
            'id',
            [
                'label' => 'Start',
                'attribute' => 'start_at',
                'value' => 'formatStartAt'
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
                'filter' => Round::dropdown(),
            ],
            [
                'label' => 'Event',
                'attribute' => 'player',
                'format' => 'raw',
                'value' => function($model) {
                    return Html::a($model->fullName, ['/event/view', 'id' => $model->id]);
                }
            ],
            [
                'attribute' => 'result',
                'label' => 'Result',
                'value' =>'result',
                'filter' => Event::resultDropdown(),
            ],
            [
                'attribute' => 'total',
                'label' => 'Sets',
                'value' => 'total',
                'filter' => Event::setsDropdown(),
            ],
            [
                'attribute' => 'total_games',
                'label' => 'Games',
                'value' => 'total_games'
            ],
            [
                'label' => 'Odds',
                'attribute' => 'count_odds',
                'value' => function($model) {
                    return count($model->odds);
                },
                'filter' => [1 => 'Yes', 2 => 'No']
            ],
        ],
    ]); ?>

</div>
