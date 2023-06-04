<?php


use frontend\models\sport\Surface;
use frontend\models\sport\Tour;
use frontend\models\sport\Tournament;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\web\View;
use backend\models\TournamentSearch;
use yii\data\ActiveDataProvider;

/**
 * @var View $this
 * @var TournamentSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 */

$this->title = 'Tournaments';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="tournament-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Tournament', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'tour_id',
                'value' => 'tournamentTour.name',
                'filter' => Tour::find()->select(['name', 'id'])->indexBy('id')->column(),
            ],
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function($model) {
                    return Html::a($model->name, ['/tournament/index', 'id' => $model->id]);
                }
            ],
            [
                'attribute' => 'surface_id',
                'value' => 'tournamentSurface.name',
                'filter' => Surface::find()->select(['name', 'id'])->indexBy('id')->column(),
            ],
            [
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
