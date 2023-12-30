<?php

/**
 * @var View $this
 * @var Total[] $stats
 * @var string $title
 */

use common\helpers\TotalHelper;
use frontend\models\sport\Total;
use yii\web\View;

$avg = [];

?>

<h2 class="mt-5 text-center"><?= $title; ?></h2>

<table class="table table-striped table-bordered detail-view mb-0 mt-4 mb-5">
    <thead>
    <tr>
        <td class="text-center"><strong>Player</strong></td>
        <td class="text-center"><strong>Events</strong></td>
        <?php foreach (TotalHelper::getStatsTitle(TotalHelper::ODDS) as $title): ?>
            <td class="text-center"><strong><?= $title ?></strong></td>
        <?php endforeach; ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($stats as $stat): ?>
        <tr>
            <td class="text-center"><?= $stat->player->name; ?></td>
            <td class="text-center"><?= $stat->count_events; ?></td>
            <?php for($i = 0; $i < 5; $i++): ?>
                <?php $attr = "percentProfit{$i}"; ?>
                <?php $avg[$i] = isset($avg[$i]) ? $avg[$i] + $stat->{$attr} : $stat->{$attr}; ?>
                <td class="text-center"><?= TotalHelper::getPercent($stat->{$attr}); ?></td>
            <?php endfor; ?>
        </tr>
    <?php endforeach; ?>
    <tr>
        <td></td>
        <td></td>
        <?php foreach ($avg as $stat): ?>
            <?php $stat = round($stat / count($stats), 2); ?>
            <td class="text-center"><?= TotalHelper::getPercent($stat); ?></td>
        <?php endforeach; ?>
    </tr>
    </tbody>

</table>