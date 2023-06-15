<?php


use common\helpers\EventHelper;
use frontend\models\sport\Event;
use frontend\models\sport\Round;
use frontend\models\sport\Tournament;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\View;
use backend\models\TournamentEventSearch;
use yii\data\ActiveDataProvider;
use yii\widgets\LinkPager;

/**
 * @var View $this
 * @var Tournament $model
 * @var TournamentEventSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 */

$this->title = $model->name;

$this->params['breadcrumbs'][] = ['label' => 'Tournaments', 'url' => ['/tournament']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ["/tournament/{$model->id}"]];
$this->params['breadcrumbs'][] = 'Events';

$reset = "/tournament/{$model->id}/event";

?>

<div class="tournament-index">

    <h4><?php echo "{$model->tournamentTour->name}: {$model->tournamentSurface->name}" ?></h4>
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Clear', [$reset], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => 'Total: {totalCount}',
        'pager' => [
            'linkContainerOptions' => [
                'class' => 'page-item'
            ],
            'linkOptions' => [
                'class' => 'page-link'
            ],
            'disabledListItemSubTagOptions' => [
                'class' => 'page-link'
            ],
            'pagination' => $dataProvider->pagination,
            'class' => LinkPager::class
        ],
        'columns' => [
            [
                'label' => 'Start',
                'attribute' => 'start_at',
                'value' => 'formatStartAt',
                'filter' => '',
            ],
            [
                'label' => 'Round',
                'attribute' => 'round_id',
                'value' => 'tournamentRound.name',
                'filter' => Round::dropdown(),
            ],
            [
                'label' => 'Event',
                'attribute' => 'player',
                'format' => 'raw',
                'value' => function($model) {
                    return Html::a($model->fullName, ['/event/view', 'id' => $model->id]);
                }
            ],
            [
                'attribute' => 'result',
                'label' => 'Result',
                'value' =>'result',
                'filter' => EventHelper::resultDropdown(),
            ],
            [
                'attribute' => 'total',
                'label' => 'Sets',
                'value' => 'total',
                'filter' => EventHelper::setsDropdown(),
            ],
            [
                'attribute' => 'total_games',
                'label' => 'Games',
                'value' => 'total_games',
                'filter' => '',
            ],
            [
                'label' => 'Over',
                'value' => function(Event $model) {
                    return EventHelper::getOddStat($model->totalsOver);
                }
            ],
        ],
    ]); ?>

</div>
