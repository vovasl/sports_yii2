<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * @var array $events
 */


if(count($events) == 0) return;

?>

<table class="table table-striped table-bordered detail-view mb-0 mt-4 mb-4">
    <thead>
    <tr>
        <td colspan="9" class="text-center"><h3>Future Events</h3></td>
    </tr>
    <tr>
        <td class="text-center"><strong>#</strong></td>
        <td class="text-center"><strong>Start</strong></td>
        <td class="text-center"><strong>Tour</strong></td>
        <td class="text-center"><strong>Surface</strong></td>
        <td class="text-center"><strong>Tournament</strong></td>
        <td class="text-center"><strong>Round</strong></td>
        <td class="text-center"><strong>Event</strong></td>
        <td class="text-center"><strong>Moneyline</strong></td>
        <td class="text-center"><strong>Total</strong></td>
    </tr>
    <tbody>
    <?php foreach ($events as $k => $event): ?>
        <?php $link = Url::to(['event/view', 'id' => $event->id]); ?>
        <td class="text-center"><?= ++$k; ?></td>
        <td class="text-center"><?= $event->formatStartAt; ?></td>
        <td class="text-center"><?= $event->eventTournament->tournamentTour->name; ?></td>
        <td class="text-center"><?= $event->eventTournament->tournamentSurface->name; ?></td>
        <td class="text-center"><?= $event->eventTournament->name; ?></td>
        <td class="text-center"><?= $event->tournamentRound->name; ?></td>
        <td class="text-center"><?= Html::a("{$event->homePlayer->name} - {$event->awayPlayer->name}", $link, ['target'=>'_blank']); ?></td>
        <td class="text-center"><?= "{$event->homeMoneylineOddVal} - {$event->awayMoneylineOddVal}";?></td>
        <td class="text-center"><?= $event->total_avg_value; ?></td>
    <?php endforeach; ?>
    </thead>
</table>