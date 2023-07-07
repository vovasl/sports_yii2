<?php


use yii\web\View;

/**
 * @var View $this
 * @var array $stats
 * @var string $title
 */

?>

<tr>
    <td colspan="7" class="text-center"><strong><?= $title ?></strong></td>
</tr>
<tr>
    <td><strong>Odds</strong></td>
    <?php foreach ($stats as $stat): ?>
        <td class="text-center"><?= $stat['count'] ?></td>
    <?php endforeach; ?>
</tr>
<tr>
    <td><strong>Profit</strong></td>
    <?php foreach ($stats as $stat): ?>
        <td class="text-center"><?= $stat['profit'] ?></td>
    <?php endforeach; ?>
</tr>
