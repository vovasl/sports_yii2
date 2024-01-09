<?php

use yii\web\View;
use yii\data\ActiveDataProvider;
use backend\models\total\EventTotalSearch;
use yii\helpers\Html;

/**
 * @var View $this
 * @var EventTotalSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 */

$this->title = 'Events Total Over';

$this->params['breadcrumbs'][] = $this->title;

$reset = "/total/events-total-over";

?>

<div class="tournament-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('events-over/stats', ['dataProvider' => $dataProvider]) ?>

    <p>
        <?= Html::a('Clear', [$reset], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= $this->render('events/grid', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]); ?>

</div>
