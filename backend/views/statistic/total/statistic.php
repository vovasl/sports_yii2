<?php

use backend\models\statistic\total\StatisticSearch;
use yii\web\View;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/**
 * @var View $this
 * @var StatisticSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 */

$this->title = 'Statistic';

?>

<div class="tournament-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('statistic/grid', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider
    ]); ?>

</div>
