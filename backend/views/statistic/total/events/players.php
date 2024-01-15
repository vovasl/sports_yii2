<?php

use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array $players
 */

?>

<table class="table table-striped table-bordered detail-view mb-0 mt-4 mb-4 w-25">
    <thead>
    <tr>
        <td class="text-center"><strong>#</strong></td>
        <td class="text-center"><strong>Player</strong></td>
        <td class="text-center"><strong>Tournament</strong></td>
    </tr>
    <tbody>
    <?php foreach ($players as $k => $player): ?>
        <tr>
            <td class="text-center"><?= ++$k; ?></td>
            <td class="text-center"><?= Html::a($player['player'], $player['link'], ['target'=>'_blank']) ?></td>
            <td class="text-center"><?= Html::a($player['tournament'], $player['tournament_link'], ['target'=>'_blank']); ?></td>
        </tr>
    <?php endforeach; ?>
    </thead>
</table>
