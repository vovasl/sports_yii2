<?php


use yii\web\View;
use common\helpers\OddHelper;

/**
 * @var View $this
 * @var array $stats
 */

?>

<thead>
<tr>
    <td></td>
    <?php foreach ($stats as $k => $stat): ?>
        <td class="text-center"><strong><?= OddHelper::getStatsTitle($k, OddHelper::totalSettings()) ?></strong></td>
    <?php endforeach; ?>
</tr>
</thead>
