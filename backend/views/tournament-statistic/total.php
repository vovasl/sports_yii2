<?php


use common\helpers\EventHelper;
use common\helpers\TournamentHelper;
use yii\web\View;

/**
 * @var View $this
 * @var array $tournaments
 * @var string $tour
 * @var string $surface
 * @var int $qualifier
 */

$types = ['Over', 'Under'];

?>
<h1>Totals</h1>
<h2><?= TournamentHelper::getTourSurfaceTitle($tour, $surface) ?></h2>
<h3><?= EventHelper::getQualifierText($qualifier) ?></h3>
<h5 class="mb-5">Events: <?= TournamentHelper::getEventsCount($tournaments) ?></h5>

<?php foreach ($types as $type): ?>

    <?= $this->render('total/_outer', [
        'tournaments' => $tournaments,
        'type' => $type,
        'qualifier' => $qualifier
    ]); ?>

<?php endforeach; ?>