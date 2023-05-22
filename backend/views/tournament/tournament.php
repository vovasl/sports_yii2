<?php

/**
 * @var yii\web\View $this
 * @var Tournament $tournament
 * @var Event[] $events
 */

use frontend\models\sport\Event;
use frontend\models\sport\Tournament;

$this->title = $tournament->name;

$this->params['breadcrumbs'][] = ['label' => 'Tournaments', 'url' => ['/tournament/tournaments']];
$this->params['breadcrumbs'][] = $tournament->name;

?>

<h1><?= $tournament->name ?></h1>

<div class="container header">
    <div class="row">
        <div class="header-col col-1">ID</div>
        <div class="header-col col-2">Start</div>
        <div class="header-col col-2">Round</div>
        <div class="header-col col">Event</div>
        <div class="header-col col-1">Odds</div>
    </div>
</div>

<?php foreach ($events as $event): ?>
    <div class="container">
        <div class="row">
            <div class="col-1 text-center"><?= $event->id ?></div>
            <div class="col-2 text-center"><?= $event->formatStartAt ?></div>
            <div class="col-2 text-center"><?= $event->tournamentRound->name ?></div>
            <div class="col"><?= $event->homePlayer->name . ' - ' . $event->awayPlayer->name ?></div>
            <div class="col-1 text-center"><?= count($event->odds) ?></div>
        </div>
    </div>
<?php endforeach; ?>