<?php


use frontend\models\sport\Player;
use frontend\models\sport\Tour;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use yii\web\View;
use backend\models\PlayerSearch;
use yii\data\ActiveDataProvider;

/**
 * @var View $this
 * @var PlayerSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 */

$this->title = 'Players';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="player-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Clear', ['/player'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

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
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function(Player $model) {
                    return Html::a($model->name, [
                        "/event",
                        "EventSearch[player]" => $model->name
                    ], ['target'=>'_blank']);
                }
            ],
            //'plays',
            //'comment:ntext',
            'count_events',
            [
                'label' => 'ATP',
                'value' => function(Player $model) {
                    return count($model->getTourEvents(Tour::ATP));
                }
            ],
            [
                'label' => 'Challenger',
                'value' => function(Player $model) {
                    return count($model->getTourEvents(Tour::CHALLENGER));
                }
            ],
            'sofa_id',
            [
                'class' => ActionColumn::class,
                'template'=>'{view} {update}',
                'urlCreator' => function ($action, Player $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>


</div>