<?php


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

?>

<div class="tournament-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'tournamentTour.name',
            'name',
            'tournamentSurface.name',
            'comment:ntext',
        ],
    ]) ?>

</div>
