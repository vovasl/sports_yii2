<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;
use frontend\models\sport\Player;

/**
 * @var View $this
 * @var Player $model
 * @var ActiveForm $form
 */

?>

<div class="player-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php //echo $form->field($model, 'type')->textInput() ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'readonly' => true]) ?>

    <?php //echo $form->field($model, 'birthday')->textInput() ?>

    <?php //echo $form->field($model, 'plays')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sofa_id')->textInput() ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
