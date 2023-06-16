<?php


use common\helpers\EventHelper;
use common\helpers\TournamentHelper;
use yii\web\View;

/**
 * @var View $this
 * @var array $tournaments
 * @var $type
 * @var $qualifier
 */

$colspan = 9;
$events = [];
$total = "totals{$type}";

?>

<h1><?= "{$tournaments[0]->tournamentTour->name}: {$tournaments[0]->tournamentSurface->name}" ?></h1>
<h3><?= EventHelper::getQualifierText($qualifier). " - {$type}" ?></h3>

<table class="table table-striped table-bordered detail-view mb-0 mt-5">
    <thead>
    <tr>
        <td class="text-center"><strong>Tournament</strong></td>
        <td colspan="<?= $colspan ?>" class="text-center"><strong>Odds</strong></td>
        <td colspan="<?= $colspan ?>" class="text-center"><strong>Events</strong></td>
        <td colspan="<?= $colspan ?>" class="text-center"><strong>Percent</strong></td>
    </tr>
    </thead>
    <tbody>

    <?php foreach ($tournaments as $tournament): ?>
        <?= $this->render('total/_stat', [
            'tournament' => $tournament->name,
            'colspan' => $colspan,
            'stats' => TournamentHelper::getOddStat($tournament->events, $total, $qualifier),
        ]) ?>
        <?php $events = array_merge($events, $tournament->events) ?>
    <?php endforeach; ?>

    <?= $this->render('total/_stat', [
        'tournament' => 'Total',
        'colspan' => $colspan,
        'stats' => TournamentHelper::getOddStat($events, $total, $qualifier),
    ]) ?>

    </tbody>
</table>