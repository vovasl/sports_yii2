<?php

use common\helpers\total\PlayerHelper;
use frontend\models\sport\Odd;
use yii\web\View;
use yii\data\ActiveDataProvider;
use backend\models\statistic\total\EventTotalSearch;
use yii\helpers\Html;

/**
 * @var View $this
 * @var EventTotalSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 */

$this->title = 'Events - Total Under';

$this->params['breadcrumbs'][] = $this->title;

$reset = '/statistic/total/events-under';

?>

<div class="tournament-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('events/stats', [
        'stats' => PlayerHelper::getEventsStat($dataProvider->getModels(), Odd::ADD_TYPE['under'])
    ]); ?>

    <p>
        <?= Html::a('Clear', [$reset], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= $this->render('events/grid', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]); ?>

</div>