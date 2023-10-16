<?php


use frontend\models\sport\Event;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var Event[] $models
 * @var array $moneyline
 */

$profit = 0;
$count = 0;
$output = "";
foreach ($models->all() as $model) {

    if($moneyline['more'] == 1) {
        if($model->homeMoneyline[0]->odd <= $moneyline['limit'] || $model->awayMoneyline[0]->odd <= $moneyline['limit']) continue;
    }
    else if($moneyline['more'] == -1){
        if ($model->homeMoneyline[0]->odd > $moneyline['limit'] && $model->awayMoneyline[0]->odd > $moneyline['limit']) continue;
    }

    $link = Html::a('Link', ['/event/view', 'id' => $model->id], ['target'=>'_blank']);
    $output .= "{$model->fullInfo} {$model->result} <br>";
    $output .= "{$link}<br>";
    //if(count($model->odds) > 1) echo "More Odds <br>";
    $output .= "Moneyline: {$model->homeMoneyline[0]->oddVal} - {$model->awayMoneyline[0]->oddVal} <br>";
    foreach ($model->odds as $odd) {
        $profit += $odd->profit;
        $output .= "Total({$odd->value}): {$odd->profit}";
        break;
    }
    $count++;
    $output .= "<hr>";
}

echo "{$count} - {$profit} <hr>";

echo $output;
?>