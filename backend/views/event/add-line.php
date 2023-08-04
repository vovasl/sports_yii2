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

$this->title = 'Add Line';
$this->params['breadcrumbs'][] = $this->title;

?>

<h1><?= $this->title ?></h1>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'event_id')->dropDownList($model->getEvents(), ['prompt' => 'Select event']) ?>

<?= $form->field($model, 'type')->dropDownList($model->getTypes(), ['prompt' => 'Select event']) ?>

<?= $form->field($model, 'add_type')->dropdownList(Odd::ADD_TYPE, ['prompt' => 'Select Additional type']) ?>

<?= $form->field($model, 'player_id') ?>

<?= $form->field($model, 'value') ?>

<?= $form->field($model, 'odd') ?>

<div class="form-group">
    <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>
