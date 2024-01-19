<?php

use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array $players
 */

?>

<table class="table table-striped table-bordered detail-view mb-0 mt-4 mb-4">
    <thead>
    <tr>
        <td colspan="11" class="text-center"><h3>Players</h3></td>
    </tr>
    <tr>
        <td class="text-center"><strong>#</strong></td>
        <td class="text-center"><strong>Player</strong></td>
        <td class="text-center"><strong>Favorite</strong></td>
        <td class="text-center"><strong>Tour</strong></td>
        <td class="text-center"><strong>Surface</strong></td>
        <td class="text-center"><strong>Tournament</strong></td>
        <td class="text-center"><strong>Start</strong></td>
        <td class="text-center"><strong>Round</strong></td>
        <td class="text-center"><strong>Event</strong></td>
        <td class="text-center"><strong>Moneyline</strong></td>
        <td class="text-center"><strong>Total</strong></td>
    </tr>
    <tbody>
    <?php foreach ($players as $k => $player): ?>
        <tr>
            <td class="text-center"><?= ++$k; ?></td>
            <td class="text-center"><?= Html::a($player['player'], $player['link'], ['target'=>'_blank']) ?></td>
            <td class="text-center"><?= $player['favorite']; ?></td>
            <td class="text-center"><?= $player['tour']; ?></td>
            <td class="text-center"><?= $player['surface']; ?></td>
            <td class="text-center"><?= Html::a($player['tournament'], $player['tournament_link'], ['target'=>'_blank']); ?></td>
            <td class="text-center"><?= $player['event_start']; ?></td>
            <td class="text-center"><?= $player['round'] ?></td>
            <td class="text-center"><?= (is_null($player['event_sofa_id'])) ? Html::a($player['event'], $player['event_link'], ['target'=>'_blank']) : $player['event']; ?></td>
            <td class="text-center"><?= $player['moneyline']; ?></td>
            <td class="text-center"><?= $player['total_avg_value']; ?></td>
        </tr>
    <?php endforeach; ?>
    </thead>
</table>
