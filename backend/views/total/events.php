<?php

use backend\models\statistic\total\EventTotalSearch;
use yii\helpers\Html;
use yii\web\View;
use yii\data\ActiveDataProvider;

/**
 * @var View $this
 * @var EventTotalSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 */

$this->title = 'Events';

$this->params['breadcrumbs'][] = $this->title;

$reset = "/total/events";

?>

<div class="tournament-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Clear', [$reset], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= $this->render('events/grid', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]); ?>

</div>
