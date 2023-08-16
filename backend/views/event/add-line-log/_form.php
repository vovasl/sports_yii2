<?php

/**
 * @var View $this
 * @var Event $event
 * @var array $odds
 * @var array $log
 */

use backend\models\AddLineLogForm;
use yii\web\View;
use frontend\models\sport\Event;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin(); ?>

<?php $addForm = new AddLineLogForm(); ?>

<?= $form->field($addForm, 'save')->hiddenInput(['value'=> 1])->label(false); ?>

<div class="form-group">
    <?= Html::submitButton('Add Line', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>