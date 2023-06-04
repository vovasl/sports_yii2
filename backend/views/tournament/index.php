<?php


use frontend\models\sport\Event;
use frontend\models\sport\Tournament;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var Tournament $tournament
 * @var Event[] $events
 */

$this->title = $tournament->name;

$this->params['breadcrumbs'][] = ['label' => 'Tournaments', 'url' => ['/tournaments']];
$this->params['breadcrumbs'][] = $tournament->name;

?>

<h1><?= $tournament->name ?></h1>

<div class="container header">
    <div class="row">
        <div class="header-col col-1">ID</div>
        <div class="header-col col-2">Start</div>
        <div class="header-col col-1">Round</div>
        <div class="header-col col">Event</div>
        <div class="header-col col-1">Odds</div>
        <div class="header-col col-2">Result</div>
    </div>
</div>

<?php foreach ($events as $event): ?>
    <div class="container">
        <div class="row">
            <div class="col-1 text-center"><?= $event->id ?></div>
            <div class="col-2 text-center"><?= $event->formatStartAt ?></div>
            <div class="col-1 text-center"><?= $event->tournamentRound->name ?></div>
            <div class="col">
                <a href="<?= Url::to(['/event/index', 'id' => $event->id]) ?>"><?= $event->fullName ?></a>
            </div>
            <div class="col-1 text-center"><?= count($event->odds) ?></div>
            <div class="col-2 text-center"><?= $event->result ?></div>
        </div>
    </div>
<?php endforeach; ?>