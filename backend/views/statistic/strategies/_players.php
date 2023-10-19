<?php


use yii\web\View;

/**
 * @var View $this
 * @var array $players
 */

?>

<h3>Players</h3>
<?php foreach ($players as $player => $val): ?>
    <?= $player; ?> (<?= $val['count']; ?>): <?= $val['profit']; ?> <br>
<?php endforeach; ?>
<br>