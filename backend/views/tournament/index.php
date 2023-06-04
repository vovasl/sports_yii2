<?php


use frontend\models\sport\Round;
use frontend\models\sport\Tournament;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\View;
use backend\models\TournamentEventSearch;
use yii\data\ActiveDataProvider;

/**
 * @var View $this
 * @var Tournament $model
 * @var TournamentEventSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 */

$this->title = $model->name;

$this->params['breadcrumbs'][] = ['label' => 'Tournaments', 'url' => ['/tournaments']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="tournament-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'label' => 'Start',
                'value' => 'formatStartAt'
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
                    return Html::a($model->fullName, ['/event/index', 'id' => $model->id]);
                }
            ],
            [
                'label' => 'Odds',
                'attribute' => 'count_odds',
                'value' => function($model) {
                    return count($model->odds);
                }
            ],
            'result'
        ],
    ]); ?>

</div>
