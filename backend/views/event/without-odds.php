<?php

/**
 * @var yii\web\View $this
 * @var Event[] $events
 */

use frontend\models\sport\Event;
use yii\helpers\Url;

$this->title = 'Events without odds';
$this->params['breadcrumbs'][] = $this->title;

?>

<h1><?= $this->title ?></h1>

<div class="container header">
    <div class="row">
        <div class="header-col col-1">ID</div>
        <div class="header-col col-2">Start</div>
        <div class="header-col col-2">Tournament</div>
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
            <div class="col-2 text-center"><?= $event->eventTournament->name ?></div>
            <div class="col-2 text-center"><?= $event->tournamentRound->name ?></div>
            <div class="col">
                <a href="<?= Url::to(['/event/index', 'id' => $event->id]) ?>"><?= $event->fullName ?></a>
            </div>
            <div class="col-1 text-center"><?= count($event->odds) ?></div>
        </div>
    </div>
<?php endforeach; ?>