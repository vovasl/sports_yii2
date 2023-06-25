<?php


use frontend\models\sport\Surface;
use frontend\models\sport\Tour;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var frontend\models\sport\Tournament $model
 * @var yii\widgets\ActiveForm $form
 */

?>

<div class="tournament-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'tour')->dropDownList(Tour::dropdown(), ['prompt' => 'Select Tour']) ?>

    <?= $form->field($model, 'surface')->dropDownList(Surface::dropdown(), ['prompt' => 'Select Surface']) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'readonly' => true]) ?>

    <?= $form->field($model, 'sofa_id')->textInput() ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
