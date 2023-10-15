<?php


use backend\models\statistic\FilterModel;
use frontend\models\sport\Odd;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array $stats
 * @var FilterModel $filter
 */

?>

<h1 class="mb-5">Totals</h1>

<?= $this->render('total/_filter', ['filter' => $filter]); ?>

<?= Html::a('Tournaments',
    ['/statistic/total-tournaments', 'tour' => $filter->tour, 'surface' => $filter->surface, 'qualifier' => $filter->round],
    ['target'=>'_blank', 'class' => 'btn btn-primary mb-4']
)?>


<?php foreach (Odd::ADD_TYPE as $type) : ?>

    <?= $this->render('total/_outer', [
        'type' => $type,
        'stats' => $stats,
        'detail' => 0,
    ]) ?>

<?php endforeach; ?>