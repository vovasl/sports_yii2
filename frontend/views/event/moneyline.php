<?php

/**
 * @var yii\web\View $this
 * @var Event[] $events
 */

use frontend\models\sport\Event;

$this->title = 'Moneyline';

$tournament = '';
?>

<?php foreach ($events as $event): ?>
    <?php $newTournament = ($tournament != $event->eventTournament->name) ?>
    <table>
        <tr>
            <td style="width: 120px;"><?php if($newTournament) echo $event->eventTournament->tournamentTour->name ?></td>
            <td style="width: 100px;"><?php if($newTournament) echo $event->eventTournament->name; ?></td>
            <td style="width: 50px;"><?= $event->tournamentRound->name ?></td>
            <td style="width: 260px;"><?= $event->playerHome->name ?></td>
            <td style="width: 260px;"><?= $event->playerAway->name ?></td>
            <td style="width: 70px;"><?= $event->homeMoneyline->oddVal ?></td>
            <td style="width: 70px;"><?= $event->awayMoneyline->oddVal ?></td>
        </tr>
    </table>
    <?php $tournament = $event->eventTournament->name; ?>
<?php endforeach; ?>