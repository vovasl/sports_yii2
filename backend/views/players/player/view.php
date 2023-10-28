<?php


use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;
use yii\web\View;
use frontend\models\sport\Player;

/**
 * @var View $this
 * @var Player $model
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Players', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

YiiAsset::register($this);

?>
<div class="player-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            //'type',
            'name',
            //'birthday',
            //'plays',
            'sofa_id',
            'comment:ntext',
        ],
    ]) ?>

</div>
