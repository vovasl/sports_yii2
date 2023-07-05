<?php


use frontend\models\sport\Event;
use yii\web\View;

/**
 * @var View $this
 * @var Event $model
 */

$this->title = $model->fullName;

$this->render('_breadcrumbs', [
    'model' => $model
]);

?>

<div class="event-update">

    <h4><?= $model->formatStartAt . ' ' . $model->fullName .' ' . $model->result ?></h4>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
