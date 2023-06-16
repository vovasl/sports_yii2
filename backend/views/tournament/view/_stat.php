<?php

/**
 * @var yii\web\View $this
 * @var string $title
 * @var array $stats
 */

use common\helpers\TournamentHelper;


?>

<table class="table table-striped table-bordered detail-view mb-0">
    <thead>
    <tr>
        <td colspan="8" class="text-center"><h4><?= $title ?></h4></td>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>Odds</td>
        <?php foreach ($stats as $percent => $stat): ?>
            <td><?= $percent; ?></td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td>Events</td>
        <?php foreach ($stats as $percent => $stat): ?>
            <td><?= count($stat['events']); ?></td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td>Percent</td>
        <?php foreach ($stats as $percent => $stat): ?>
            <td>
                <?= $stat['percent'] ?><?php if($percent !== TournamentHelper::STAT_EMPTY_KEY):?>%<?php endif; ?>
            </td>
        <?php endforeach; ?>
    </tr>
    </tbody>
</table>