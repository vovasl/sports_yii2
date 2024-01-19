<?php


use backend\models\statistic\total\StatisticSearch;
use common\helpers\EventHelper;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;
use frontend\models\sport\Tournament;
use yii\web\View;

/**
 * @var View $this
 * @var Tournament $model
 * @var StatisticSearch $totalSearchModel
 * @var ActiveDataProvider $totalDataProvider
 */


$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Tournaments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

YiiAsset::register($this);

?>

<div class="tournament-view">

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Events', ['/event', 'EventSearch[tournament_id]' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'tournamentTour.name',
            'name',
            'tournamentSurface.name',
            [
                'label' => 'Events',
                'value' => count($model->events)
            ],
            [
                'label' => 'Main',
                'value' => EventHelper::getCount($model->events)
            ],
            [
                'label' => 'Qualifiers',
                'value' => EventHelper::getCount($model->events, 1)
            ],
            'comment:ntext',
        ],
    ]) ?>

    <?= $this->render('/statistic/total/statistic', [
        'dataProvider' => $totalDataProvider,
        'searchModel' => $totalSearchModel,
    ]); ?>

</div>
