<?php

/**
 * @var yii\web\View $this
 * @var Tournament[] $tournaments
 */

use frontend\models\sport\Tournament;

$this->title = 'Tournaments';

?>

<?php foreach ($tournaments as $tournament): ?>
    <?= $tournament->name; ?>
    <?= $tournament->count_events; ?>
    <br>
<?php endforeach; ?>
