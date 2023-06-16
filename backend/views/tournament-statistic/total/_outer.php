<?php


use common\helpers\TournamentHelper;

/**
 * @var yii\web\View $this
 * @var array $tournaments
 * @var string $type
 * @var int $qualifier
 */

$colspan = 9;
$events = [];
$total = "totals{$type}";

?>

<h3><?= $type ?></h3>

<table class="table table-striped table-bordered detail-view mb-0 mt-3 mb-5">
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
        <?= $this->render('_body', [
            'tournament' => $tournament->name,
            'colspan' => $colspan,
            'stats' => TournamentHelper::getOddStat($tournament->events, $total, $qualifier),
        ]) ?>
        <?php $events = array_merge($events, $tournament->events) ?>
    <?php endforeach; ?>

    <?= $this->render('_body', [
        'tournament' => 'Total',
        'colspan' => $colspan,
        'stats' => TournamentHelper::getOddStat($events, $total, $qualifier),
    ]) ?>

    </tbody>
</table>
