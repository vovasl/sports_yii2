<?php


use frontend\models\sport\Event;
use frontend\models\sport\Odd;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var Event $model
 * @var Odd $odd
 */

?>

<?= $model->fullInfo; ?> <?= $model->result; ?> <br>
<?= Html::a('Link', ['/event/view', 'id' => $model->id], ['target'=>'_blank']); ?><br>
Moneyline: <?= $model->homeMoneyline[0]->oddVal; ?> - <?= $model->awayMoneyline[0]->oddVal; ?> <br>
Profit(<?= $odd->value; ?>): <?= $odd->profit; ?>
<hr>
