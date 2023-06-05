<?php

/**
 * @var yii\web\View $this
 * @var Event $event
 */

use frontend\models\sport\Event;

$this->title = $event->fullName;

$this->params['breadcrumbs'][] = ['label' => 'Tournaments', 'url' => ['/tournaments']];
$this->params['breadcrumbs'][] = ['label' => $event->eventTournament->name, 'url' => ["/tournaments/{$event->tournament}/events"]];
$this->params['breadcrumbs'][] = $event->fullName;

?>

<h4><?= $event->formatStartAt . ' ' . $event->tournamentRound->name . ' ' . $event->fullName .' ' . $event->result ?></h4>

<?= $this->render('_games', ['event' => $event]) ?>
<?= $this->render('_teams', ['event' => $event]) ?>
<?= $this->render('_sets', ['event' => $event]) ?>