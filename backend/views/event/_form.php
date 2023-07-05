<?php


use frontend\models\sport\Round;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;
use frontend\models\sport\Event;

/**
 * @var View $this
 * @var Event $model
 * @var ActiveForm $form
 */

?>

<div class="event-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'round')->dropDownList(Round::dropdown()) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
