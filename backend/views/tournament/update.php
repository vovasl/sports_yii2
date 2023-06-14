<?php


use yii\helpers\Html;
use yii\web\View;
use frontend\models\sport\Tournament;

/**
 * @var View $this
 * @var Tournament $model
 */

$this->title = 'Update Tournament: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Tournaments', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';

?>

<div class="tournament-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
