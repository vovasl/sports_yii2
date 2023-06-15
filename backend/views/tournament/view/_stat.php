<?php

/**
 * @var yii\web\View $this
 * @var string $title
 * @var array $stats
 */


?>

<table class="w-25 table table-striped table-bordered detail-view mr-5">
    <thead>
        <tr>
            <th colspan="3" class="text-center"><h5><?= $title ?></h5></th>
        </tr>
        <tr>
            <th>Odds</th>
            <th>Events</th>
            <th>Percent</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($stats as $percent => $stat): ?>
        <tr>
            <td>
                <?= $percent ?><?php if($percent != 'no'):?>%<?php endif; ?>
            </td>
            <td><?= count($stat['events']); ?></td>
            <td>
                <?= $stat['percent'] ?><?php if($percent != 'no'):?>%<?php endif; ?>
            </td>
            <!--<td><?php \backend\components\pinnacle\helpers\BaseHelper::outputArray($stat['events']) ?></td>-->
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>