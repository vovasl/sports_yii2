<?php


use yii\web\View;

/**
 * @var string $output
 * @var array $players
 */


?>

<?= $output ?>

<h3>Players</h3>
<?php foreach ($players as $player => $val): ?>
    <?= $player; ?> (<?= $val['count']; ?>): <?= $val['profit']; ?> <br>
<?php endforeach; ?>
