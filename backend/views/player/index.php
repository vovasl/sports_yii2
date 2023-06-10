<?php


use frontend\models\sport\Player;
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
        <?= Html::a('Create Player', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Reset', ['/player'], ['class' => 'btn btn-primary']) ?>
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
            'name',
            //'plays',
            //'comment:ntext',
            'sofa_id',
            [
                'attribute' => 'events',
                'value' => function (Player $model) {
                    return count($model->events);
                }
            ],
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
