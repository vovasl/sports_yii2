<?php


use frontend\models\sport\Surface;
use frontend\models\sport\Tour;
use frontend\models\sport\Tournament;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\web\View;
use backend\models\TournamentsSearch;
use yii\data\ActiveDataProvider;

/**
 * @var View $this
 * @var TournamentsSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 */

$this->title = 'Tournaments';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="tournament-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Reset', ['/tournaments'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => 'Total: {totalCount}',
        'columns' => [
            [
                'label' => 'Tour',
                'attribute' => 'tour_id',
                'value' => 'tournamentTour.name',
                'filter' => Tour::dropdown(),
            ],
            [
                'label' => 'Surface',
                'attribute' => 'surface_id',
                'value' => 'tournamentSurface.name',
                'filter' => Surface::dropdown(),
            ],
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function($model) {
                    return Html::a($model->name, ['/tournament/index', 'id' => $model->id]);
                }
            ],
            [
                'label' => 'Events',
                'attribute' => 'count_events',
                'value' => function($model) {
                    return count($model->events);
                }
            ],
            [
                'class' => ActionColumn::class,
                'template'=>'{view} {update}',
                'urlCreator' => function ($action, Tournament $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

</div>
