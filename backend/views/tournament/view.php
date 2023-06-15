<?php


use common\helpers\EventHelper;
use common\helpers\TournamentHelper;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;
use frontend\models\sport\Tournament;
use yii\web\View;

/**
 * @var View $this
 * @var Tournament $model
 */


$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Tournaments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

YiiAsset::register($this);

$totalsOver = TournamentHelper::getOddStat($model->events, 'totalsOver');

?>

<div class="tournament-view">

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Events', ["/tournament/{$model->id}/event"], ['class' => 'btn btn-primary']) ?>
    </p>

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'tournamentTour.name',
            'name',
            'tournamentSurface.name',
            'comment:ntext',
        ],
    ]) ?>

    <h4>Total Over</h4>

    <table class="table table-striped table-bordered detail-view">
        <tbody>
            <?php foreach ($totalsOver as $k => $val): ?>
                <tr><th><?= $k ?>%</th><td><?= $val ?></td></tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>
