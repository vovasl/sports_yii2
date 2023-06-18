<?php


use yii\web\View;
use common\helpers\OddHelper;

/**
 * @var View $this
 * @var string $type
 * @var array $stats
 */

?>

<h3><?php echo ucfirst($type); ?></h3>

<table class="table table-striped table-bordered detail-view mb-0 mt-3 mb-5">
    <thead>
    <tr>
        <td></td>
        <?php foreach ($stats as $k => $stat): ?>
            <td class="text-center"><strong><?= OddHelper::getStatsTitle($k, OddHelper::totalSettings()) ?></strong></td>
        <?php endforeach; ?>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td><strong>Odds</strong></td>
        <?php foreach ($stats as $k => $stat): ?>
            <td class="text-center"><?= $stat['count'] ?></td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td><strong>Profit</strong></td>
        <?php foreach ($stats as $k => $stat): ?>
            <td class="text-center"><?= $stat['profit'] ?></td>
        <?php endforeach; ?>
    </tr>
    </tbody>
</table>
