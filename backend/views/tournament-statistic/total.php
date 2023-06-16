<?php


use common\helpers\EventHelper;
use yii\web\View;

/**
 * @var View $this
 * @var array $tournaments
 * @var string $type
 * @var int $qualifier
 */

$types = ['Over', 'Under'];

$count = 0;
foreach ($tournaments as $tournament) {
    $count += $tournament->count_events;
}


?>

<h1><?= "{$tournaments[0]->tournamentTour->name}: {$tournaments[0]->tournamentSurface->name}" ?></h1>
<h3><?= EventHelper::getQualifierText($qualifier) ?></h3>
<h5 class="mb-5">Events: <?= $count ?></h5>

<?php foreach ($types as $type): ?>

    <?= $this->render('total/_outer', [
        'tournaments' => $tournaments,
        'type' => $type,
        'qualifier' => $qualifier
    ]); ?>

<?php endforeach; ?>