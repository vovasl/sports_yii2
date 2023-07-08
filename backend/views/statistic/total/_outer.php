<?php


use yii\web\View;
use common\helpers\OddHelper;

/**
 * @var View $this
 * @var string $type
 * @var array $tournaments
 */

$general = [];

foreach ($tournaments as $tournament) {

    $odds = [];
    foreach ($tournament->events as $event) {
        foreach ($event->odds as $odd) {
            $odds[] = $odd;
        }
    }

    $stats = OddHelper::getStats($odds, $type);
    $general[] = $stats;

    echo $this->render('_body', [
        'title' => $tournament->name,
        'stats' => $stats
    ]);
}

$res = [];
foreach ($general as $odds) {
    foreach ($odds as $k => $odd) {
        $res[$k]['count'] += $odd['count'];
        $res[$k]['profit'] += $odd['profit'];
    }
}

echo $this->render('_body', [
    'title' => 'General',
    'stats' => $res
])

?>

