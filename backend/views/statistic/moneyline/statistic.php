<?php

use yii\web\View;
use yii\helpers\Html;
use yii\data\ActiveDataProvider;
use backend\models\statistic\moneyline\StatisticSearch;

/**
 * @var View $this
 * @var StatisticSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 */

$this->title = 'Statistic';

?>

<div class="tournament-index">

    <h1><?= Html::encode($this->title); ?></h1>

    <?= $this->render('statistic/grid', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider
    ]); ?>

</div>
