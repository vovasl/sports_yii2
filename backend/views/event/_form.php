<?php


use frontend\models\sport\Player;
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

    <?php if(empty($model->sofa_id)): ?>

        <?= $form->field($model, 'home')->dropDownList(Player::dropdownSimilar($model->homePlayer->name)) ?>

        <?= $form->field($model, 'away')->dropDownList(Player::dropdownSimilar($model->awayPlayer->name)) ?>

    <?php endif; ?>

    <?= $form->field($model, 'round')->dropDownList(Round::dropdown()) ?>

    <?php //echo $form->field($model, 'winner')->dropDownList($model->dropdownPlayers()) ?>

    <?= $form->field($model, 'five_sets')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
