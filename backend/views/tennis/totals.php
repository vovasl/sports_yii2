<?php

/**
 * @var yii\web\View $this
 * @var string $oddMethod
 * @var Event[] $events
 */

use frontend\models\sport\Event;
use frontend\models\sport\Odd;

$this->title = 'Totals';

$tournament = '';
?>

<?php foreach ($events as $event): ?>
    <?php $newTournament = ($tournament != $event->eventTournament->name) ?>
    <table style="border-bottom: 1px solid black;">
        <tr>
            <td style="width: 120px;"><?php if($newTournament) echo $event->eventTournament->tournamentTour->name ?></td>
            <td style="width: 100px;"><?php if($newTournament) echo $event->eventTournament->name; ?></td>
            <td style="width: 50px;"><?= $event->tournamentRound->name ?></td>
            <td style="width: 260px;"><?= $event->playerHome->name ?></td>
            <td style="width: 260px;"><?= $event->playerAway->name ?></td>
            <td style="width: 100px;">
                <?php foreach ($event->{$oddMethod} as $odd): ?>
                    <?php /** @var Odd $odd */ ?>
                    <?= $odd->value . ' ' .  $odd->oddVal ?>
                    <br>
                <?php endforeach; ?>
            </td>
        </tr>
    </table>
    <?php $tournament = $event->eventTournament->name; ?>
<?php endforeach; ?>
