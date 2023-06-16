<?php

/**
 * @var yii\web\View $this
 * @var string $tournament
 * @var string $colspan
 * @var array $stats
 */

use common\helpers\TournamentHelper;

$emptyCount = $colspan - count($stats);
$emptyBlock = str_repeat('<td></td>', $emptyCount);

?>

<tr>

    <td><strong><?= $tournament ?></strong></td>

    <?php foreach ($stats as $percent => $stat): ?>
        <td><?= $percent; ?></td>
    <?php endforeach; ?>
    <?= $emptyBlock; ?>

    <?php foreach ($stats as $percent => $stat): ?>
        <td><?= count($stat['events']); ?></td>
    <?php endforeach; ?>
    <?= $emptyBlock; ?>

    <?php foreach ($stats as $percent => $stat): ?>
        <td>
            <?= $stat['percent'] ?><?php if($percent !== TournamentHelper::STAT_EMPTY_KEY):?>%<?php endif; ?>
        </td>
    <?php endforeach; ?>
    <?= $emptyBlock; ?>

</tr>