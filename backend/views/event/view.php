<?php

/**
 * @var yii\web\View $this
 * @var Event $event
 */

use common\helpers\TotalHelper;
use frontend\models\sport\Event;
use frontend\models\sport\Odd;
use frontend\models\sport\Round;
use frontend\models\sport\Statistic;

$this->title = $event->fullName;

$this->render('_breadcrumbs', [
    'model' => $event
]);

?>

<h4><?= $event->formatStartAt . ' ' . $event->tournamentRound->name . ' ' . $event->fullNameLink .' ' . $event->result ?></h4>

<?php if(count($event->odds) > 0): ?>
    <?= $this->render('view/_games', ['event' => $event]); ?>
    <?= $this->render('view/_teams', ['event' => $event]); ?>
    <?= $this->render('view/_sets', ['event' => $event]); ?>
<?php endif; ?>

<?= $this->render('view/_total_stats', [
    'title' => 'vs Over Players',
    'stats' => TotalHelper::getEventPlayerStatAgainstOverPlayers($event),
    'playerUrlParams' => [
        '/statistic/total/players-over',
        'PlayerTotalSearch[tour]' => TotalHelper::getTour($event->eventTournament->tour),
        'PlayerTotalSearch[surface]' => TotalHelper::getSurface($event->eventTournament->surface),
        'PlayerTotalSearch[round]' => Round::MAIN,
        'PlayerTotalSearch[min_moneyline]' => Statistic::TOTAL_FILTER['moneyline']['equal'],
        'PlayerTotalSearch[five_sets]' => $event->five_sets,
        'PlayerTotalSearch[add_type]' => Odd::ADD_TYPE['over'],
    ],
]); ?>

<?= $this->render('view/_total_stats', [
    'title' => 'Total Over Equal',
    'stats' => TotalHelper::getEventPlayersStat($event, Odd::ADD_TYPE['over']),
    'playerUrlParams' => [
        '/statistic/total/players',
        'PlayerTotalSearch[tour]' => TotalHelper::getTour($event->eventTournament->tour),
        'PlayerTotalSearch[surface]' => $event->eventTournament->surface,
        'PlayerTotalSearch[round]' => Round::MAIN,
        'PlayerTotalSearch[min_moneyline]' => Statistic::TOTAL_FILTER['moneyline']['equal'],
        'PlayerTotalSearch[five_sets]' => $event->five_sets,
        'PlayerTotalSearch[add_type]' => Odd::ADD_TYPE['over'],
    ],
]); ?>

<?= $this->render('view/_total_stats', [
    'title' => 'Total Over Favorite',
    'stats' => TotalHelper::getEventPlayersStat($event, Odd::ADD_TYPE['over'], 1),
    'playerUrlParams' => [
        '/statistic/total/players',
        'PlayerTotalSearch[tour]' => TotalHelper::getTour($event->eventTournament->tour),
        'PlayerTotalSearch[surface]' => $event->eventTournament->surface,
        'PlayerTotalSearch[round]' => Round::MAIN,
        'PlayerTotalSearch[min_moneyline]' => Statistic::TOTAL_FILTER['moneyline']['favorite'],
        'PlayerTotalSearch[five_sets]' => $event->five_sets,
        'PlayerTotalSearch[add_type]' => Odd::ADD_TYPE['over'],
        'PlayerTotalSearch[favorite]' => 'Yes',
    ],
]); ?>

<?= $this->render('view/_odd_move', ['event' => $event]); ?>
