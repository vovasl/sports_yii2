<?php


use frontend\models\sport\Event;
use frontend\models\sport\Round;
use yii\web\View;
use frontend\models\sport\Odd;
use common\helpers\OddHelper;

/**
 * @var View $this
 * @var string $type
 * @var array $tournaments
 * @var int $qualifier
 */

$general = [];
$res = [];

?>

<?php foreach ($tournaments as $index => $tournament): ?>

<?php

$odds = Odd::find();
$odds->joinWith(['eventOdd', 'eventOdd.eventTournament']);
$odds->where(['type' => 2]);
$odds->andWhere(['IS NOT', 'profit', NULL]);
$odds->andWhere([Event::tableName() . '.tournament' => $tournament->id]);
if($qualifier == -1) $odds->andWhere(['!=' , Event::tableName() . '.round', Round::QUALIFIER]);
else if($qualifier == 1) $odds->andWhere(['=' , Event::tableName() . '.round', Round::QUALIFIER]);

/*
$odds = [];
foreach ($tournament->events as $event) {
    foreach ($event->odds as $odd) {
        if($odd->type == 2 && $odd->profit != null) $odds[] = $odd;
    }
}
echo count($odds); die;

echo $odds->count(); die;
*/

$stats = OddHelper::getStats($odds->all(), $type);

$general[] = $stats;

?>

<?php if($index == 0): ?>
<?= $this->render('_head', [
    'stats' => $stats,
]) ?>
<tbody>
<?php endif; ?>

<?= $this->render('_body', [
    'title' => $tournament->name,
    'stats' => $stats
]) ?>

<?php endforeach; ?>

<?php
foreach ($general as $odds) {
    foreach ($odds as $k => $odd) {
        $res[$k]['count'] += $odd['count'];
        $res[$k]['profit'] += $odd['profit'];
    }
}
?>

<?= $this->render('_body', [
    'title' => 'General',
    'stats' => $res
]) ?>
</tbody>

