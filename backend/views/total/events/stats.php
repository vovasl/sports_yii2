<?php

use common\helpers\TotalHelper;
use yii\web\View;

/**
 * @var View $this
 * @var array $stats
 */

?>

<h3 class="text-center">Statistic</h3>

<table class="table table-striped table-bordered detail-view mb-0 mt-4 mb-5">
    <thead>
    <tr>
        <td class="text-center"><strong></strong></td>
        <?php foreach (TotalHelper::getStatsTitle(TotalHelper::ODDS) as $title): ?>
            <td class="text-center"><strong><?= $title ?></strong></td>
        <?php endforeach; ?>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="text-center">Profit</td>
        <?php foreach (array_keys(TotalHelper::ODDS) as $i): ?>
            <td class="text-center"><?= $stats[$i]['profit'] ?? 0; ?></td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="text-center">Events</td>
        <?php foreach (array_keys(TotalHelper::ODDS) as $i): ?>
            <td class="text-center"><?= $stats[$i]['count'] ?? 0; ?></td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="text-center">ROI</td>
        <?php foreach (array_keys(TotalHelper::ODDS) as $i): ?>
            <td class="text-center"><?= isset($stats[$i]['profit']) ? round($stats[$i]['profit'] / $stats[$i]['count'] / 100, 2) :  0; ?>%</td>
        <?php endforeach; ?>
    </tr>
    </tbody>

</table>
