<?php

/**
 * @var View $this
 * @var Event $event
 * @var array $stat
 * @var array $history
 */

use yii\web\View;
use common\helpers\statistic\total\event\player\EqualHelper;
use common\helpers\statistic\total\event\player\FavoriteHelper;
use common\helpers\statistic\total\event\player\OverPlayerHelper;
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

<?php if(count($stat['total_over_vs_over_players']) > 0) { ?>

    <?= $this->render('view/_total_stats', [
        'title' => 'vs Over Players',
        'stats' => $stat['total_over_vs_over_players'],
        'playerUrlParams' => [
            '/statistic/total/players-over',
            'PlayerTotalSearch[tour]' => OverPlayerHelper::getTourFilter($event),
            'PlayerTotalSearch[surface]' => $event->eventTournament->surface,
            'PlayerTotalSearch[round]' => Round::MAIN,
            'PlayerTotalSearch[min_moneyline]' => Statistic::TOTAL_FILTER['moneyline']['equal'],
            'PlayerTotalSearch[five_sets]' => $event->five_sets,
            'PlayerTotalSearch[add_type]' => Odd::ADD_TYPE['over'],
        ],
    ]); ?>

<?php } ?>

<?php if(count($stat['total_over']) > 0) { ?>

    <?= $this->render('view/_total_stats', [
        'title' => 'Total Over Equal',
        'stats' => $stat['total_over'],
        'playerUrlParams' => [
            '/statistic/total/players',
            'PlayerTotalSearch[tour]' => EqualHelper::getTourFilter($event),
            'PlayerTotalSearch[surface]' => $event->eventTournament->surface,
            'PlayerTotalSearch[round]' => EqualHelper::getRoundFilter($event),
            'PlayerTotalSearch[min_moneyline]' => Statistic::TOTAL_FILTER['moneyline']['equal'],
            'PlayerTotalSearch[five_sets]' => $event->five_sets,
            'PlayerTotalSearch[add_type]' => Odd::ADD_TYPE['over'],
        ],
    ]); ?>

<?php } ?>

<?php if(count($stat['total_over_favorite']) > 0) { ?>

    <?= $this->render('view/_total_stats', [
        'title' => 'Total Over Favorite',
        'stats' => $stat['total_over_favorite'],
        'playerUrlParams' => FavoriteHelper::getPlayerUrlParams($event),
        'favorite' => true,
    ]); ?>

<?php } ?>

<?php if(count($history) > 0) { ?>

    <?= $this->render('view/_odd_move', [
        'event' => $event,
        'history' => $history,
    ]); ?>

<?php } ?>
