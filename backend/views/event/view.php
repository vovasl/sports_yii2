<?php

/**
 * @var yii\web\View $this
 * @var Event $event
 */

use frontend\models\sport\Event;

$this->title = $event->fullName;

$this->params['breadcrumbs'][] = ['label' => 'Tournaments', 'url' => ['/tournament']];
$this->params['breadcrumbs'][] = ['label' => $event->eventTournament->name, 'url' => ["/tournament/{$event->tournament}"]];
$this->params['breadcrumbs'][] = ['label' => 'Events', 'url' => ["/tournament/{$event->tournament}/event"]];
$this->params['breadcrumbs'][] = $event->fullName;

?>

<h4><?= $event->formatStartAt . ' ' . $event->tournamentRound->name . ' ' . $event->fullName .' ' . $event->result ?></h4>

<?= $this->render('view/_games', ['event' => $event]) ?>
<?= $this->render('view/_teams', ['event' => $event]) ?>
<?= $this->render('view/_sets', ['event' => $event]) ?>