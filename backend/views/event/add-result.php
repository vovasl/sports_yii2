<?php

/**
 * @var yii\web\View $this
 * @var AddResultForm $model
 */

use backend\models\AddResultForm;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$this->title = 'Add event result';
$this->params['breadcrumbs'][] = $this->title;

?>

<h1><?= $this->title ?></h1>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'id')->dropDownList($model->getEvent(), ['prompt' => 'Select event']) ?>

<?= $form->field($model, 'sofa_id') ?>

<?= $form->field($model, 'result') ?>

<div class="form-group">
    <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>
