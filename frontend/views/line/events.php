<?php

/**
 * @var yii\web\View $this
 * @var array $oddMethods
 * @var Event[] $events
 */

use frontend\models\sport\Event;

$this->title = 'Events';

$tournament = '';
?>

<?php foreach ($events as $event): ?>
    <?php $newTournament = ($tournament != $event->eventTournament->name) ?>
    <table style="border-bottom: 1px solid black;">
        <tr>
            <td style="width: 120px;"><?php if($newTournament) echo $event->eventTournament->tournamentTour->name ?></td>
            <td style="width: 100px;"><?php if($newTournament) echo $event->eventTournament->name; ?></td>
            <td style="width: 100px"><?= $event->formatStartAt ?></td>
            <td style="width: 120px;"><?= $event->tournamentRound->name ?></td>
            <td style="width: 260px;"><?= $event->playerHome->name ?></td>
            <td style="width: 260px;"><?= $event->playerAway->name ?></td>
            <?php foreach ($oddMethods as $method): ?>
                <td style="width: 100px;">
                    <?= $this->render('_odd', [
                        'event' => $event,
                        'method' => $method
                    ]) ?>
                </td>
            <?php endforeach; ?>
        </tr>
    </table>
    <?php $tournament = $event->eventTournament->name; ?>
<?php endforeach; ?>
