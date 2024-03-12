<?php

/**
 * @var View $this
 * @var array $data
 */

use backend\services\statistic\event\total\FavoritePlayer;
use common\helpers\TotalHelper;
use yii\helpers\Html;
use yii\web\View;

$avg = [];

?>

<h3 class="text-center"><?= $data['title']; ?></h3>

<table class="table table-striped table-bordered detail-view mb-0 mt-4 mb-3">
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
    <?php foreach ($data['statistic'] as $k => $stat): ?>
        <?php
            /** additional url filter params */
            $data['url']['PlayerTotalSearch[player_name]'] = $stat->player->name;
            if(!empty($data['favorite'])) {
                $data['url']['PlayerTotalSearch[favorite]'] = FavoritePlayer::getFavoriteFilter($k);
            }
        ?>
        <tr>
            <td class="text-center"><?= Html::a($stat->player->name, $data['url'], ['target'=>'_blank']); ?></td>
            <td class="text-center"><?= $stat->count_events; ?></td>
            <?php for($i = 0; $i < 5; $i++): ?>
                <?php $attr = "percent_profit_{$i}"; ?>
                <?php $attrOutput = "percentProfit{$i}"; ?>
                <?php $avg[$i] = isset($avg[$i]) ? $avg[$i] + $stat->{$attr} : $stat->{$attr}; ?>
                <td class="text-center"><?= $stat->{$attrOutput}; ?></td>
            <?php endfor; ?>
        </tr>
    <?php endforeach; ?>
    <tr>
        <td class="text-center">AVG ROI</td>
        <td></td>
        <?php foreach ($avg as $stat): ?>
            <?php $stat = round($stat / count($data['statistic']), 2); ?>
            <td class="text-center"><?= TotalHelper::getPercent($stat); ?></td>
        <?php endforeach; ?>
    </tr>
    </tbody>

</table>