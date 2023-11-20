<?php

/**
 * @var View $this
 * @var Event $event
 */

use backend\helpers\total\OverHelper;
use common\helpers\OddHelper;
use frontend\models\sport\Event;
use yii\web\View;

$stats = OverHelper::getEventPlayersStat($event);

?>

<h2 class="mt-5 text-center">Total Over Stats</h2>

<table class="table table-striped table-bordered detail-view mb-0 mt-4 mb-5">
    <thead>
    <tr>
        <td class="text-center"><strong>Player</strong></td>
        <?php foreach (OddHelper::getStatsTitle(OddHelper::totalSettings()) as $title): ?>
            <td class="text-center"><strong><?= $title ?></strong></td>
        <?php endforeach; ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($stats as $player): ?>
        <tr>
            <?php foreach ($player as $stat): ?>
                <td class="text-center"><?= $stat; ?></td>
            <?php endforeach; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>

</table>