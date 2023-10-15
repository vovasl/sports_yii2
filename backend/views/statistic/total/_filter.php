<?php


use backend\models\statistic\FilterModel;
use frontend\models\sport\Round;
use frontend\models\sport\Surface;
use frontend\models\sport\Tour;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var View $this
 * @var FilterModel $filter
 */

?>

<?php $form = ActiveForm::begin([]); ?>

<div class="form-row align-items-center mb-4">

    <div class="col-auto">
        <?= $form->field($filter, 'tour')
            ->dropDownList(Tour::dropdown(), ['prompt' => 'Select tour'])
        ; ?>
    </div>

    <div class="col-auto">
        <?= $form->field($filter, 'surface')
            ->dropDownList(Surface::dropdown(), ['prompt' => 'Select surface'])
        ; ?>
    </div>

    <div class="col-auto">
        <?= $form->field($filter, 'round')
            ->dropDownList(Round::dropdownFilterWithAll())
        ; ?>
    </div>

    <div class="col-auto">
        <?= $form->field($filter, 'value')
            ->textInput(['style' => 'width: 60px;'])
        ; ?>
    </div>

    <div class="col-auto">
        <?= $form->field($filter, 'five_sets')
            ->checkbox(['class' => 'ml-2 mt-5'])
        ; ?>
    </div>

    <div class="col-auto">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary ml-3 mt-3']) ?>
    </div>

</div>

<?php ActiveForm::end(); ?>
