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

?>

<h1><?= "{$tournaments[0]->tournamentTour->name}: {$tournaments[0]->tournamentSurface->name}" ?></h1>
<h3 class="mb-5"><?= EventHelper::getQualifierText($qualifier) ?></h3>

<?php foreach ($types as $type): ?>

    <?= $this->render('total/_outer', [
        'tournaments' => $tournaments,
        'type' => $type,
        'qualifier' => $qualifier
    ]); ?>

<?php endforeach; ?>