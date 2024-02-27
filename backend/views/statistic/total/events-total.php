<?php

use yii\helpers\Url;
use yii\web\View;
use yii\data\ActiveDataProvider;
use backend\models\statistic\total\EventTotalSearch;
use yii\helpers\Html;

/**
 * @var View $this
 * @var string $title
 * @var string $url
 * @var EventTotalSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 * @var array $players
 * @var array $statistic
 */

$this->title = $title;

$this->params['breadcrumbs'][] = $this->title;

?>

<div class="tournament-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Clear', [$url], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= $this->render('events/grid', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]); ?>

    <?= $this->render('events/players', [
        'players' => $players,
    ]); ?>

    <?= $this->render('events/stats', [
        'stats' => $statistic,
    ]); ?>

</div>
