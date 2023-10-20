<?php


use common\helpers\EventFilterHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array $strategies
 * @var array $config
 */

?>

<h1>Total Over</h1>
<hr>

<?php foreach ($strategies as $strategy): ?>
    <?php $models = EventFilterHelper::getTotalOver($config, $strategy); ?>
    <?php foreach ($models as $model): ?>
        <?= $model->fullInfo; ?> <br>
        <?= Html::a('Link', ['/event/view', 'id' => $model->id], ['target'=>'_blank']); ?><br>
        Moneyline: <?= $model->homeMoneyline[0]->oddVal; ?> - <?= $model->awayMoneyline[0]->oddVal; ?> <br>
        Odd(<?= $model->o_value; ?>): <?= $model->oddVal; ?>
        <hr>
    <?php endforeach; ?>
<?php endforeach; ?>