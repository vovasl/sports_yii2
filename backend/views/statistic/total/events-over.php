<?php

use common\helpers\total\PlayerHelper;
use frontend\models\sport\Odd;
use frontend\models\sport\PlayerTotal;
use yii\web\View;
use yii\data\ActiveDataProvider;
use backend\models\statistic\total\EventTotalSearch;
use yii\helpers\Html;

/**
 * @var View $this
 * @var EventTotalSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 * @var array $eventIds
 */

$this->title = 'Events - Total Over';

$this->params['breadcrumbs'][] = $this->title;

$reset = '/statistic/total/events-over';

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

    <?= $this->render('events/players', [
        'players' => PlayerHelper::getPlayers([Odd::ADD_TYPE['over'], PlayerTotal::TYPE['over-favorite']]),
    ]); ?>

    <?= $this->render('events/stats', [
        'stats' => PlayerHelper::getEventsStat($dataProvider->getModels()),
    ]); ?>

</div>
