<?php


use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var frontend\models\sport\Tournament $model
 */

$this->title = 'Create Tournament';
$this->params['breadcrumbs'][] = ['label' => 'Tournaments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="tournament-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
