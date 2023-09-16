<?php

/**
 * @var View $this
 * @var AddLineForm $model
*/

use backend\models\AddLineForm;
use frontend\models\sport\Odd;
use yii\web\View;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\web\JqueryAsset;

$this->title = 'Add Line';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('/js/add-line.js', ['depends' => [JqueryAsset::class]]);

?>

<h1><?= $this->title ?></h1>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'event_id')->dropDownList($model->getEvents(), ['prompt' => 'Select event']); ?>

<?= $form->field($model, 'type')->dropDownList($model->getTypes(), ['prompt' => 'Select type']); ?>

<?= $form->field($model, 'add_type')->dropdownList(Odd::ADD_TYPE, ['prompt' => 'Select Additional type']); ?>

<?= $form->field($model, 'player_id')->dropdownList($model->getPlayers(), ['prompt' => 'Select Player']); ?>

<?= $form->field($model, 'value'); ?>

<?= $form->field($model, 'odd_home'); ?>

<?= $form->field($model, 'odd_away'); ?>

<?= $form->field($model, 'odd_over'); ?>

<?= $form->field($model, 'odd_under'); ?>

<?= $form->field($model, 'close')->checkbox() ?>

<div class="form-group">
    <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>
