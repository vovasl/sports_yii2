<?php


use common\helpers\TournamentHelper;

/**
 * @var yii\web\View $this
 * @var array $events
 */

?>

<h3>Totals</h3>

<div class="container">
    <div class="row">

        <?= $this->render('_stat', [
            'title' => 'Total Over',
            'stats' => TournamentHelper::getOddStat($events, 'totalsOver')
        ]) ?>

        <?= $this->render('_stat', [
            'title' => 'Total Over - Main',
            'stats' => TournamentHelper::getOddStat($events, 'totalsOver', -1)
        ]) ?>

        <?= $this->render('_stat', [
            'title' => 'Total Over - Qualifiers',
            'stats' => TournamentHelper::getOddStat($events, 'totalsOver', 1)
        ]) ?>

    </div>
</div>

<div class="container">
    <div class="row">

        <?= $this->render('_stat', [
            'title' => 'Total Under',
            'stats' => TournamentHelper::getOddStat($events, 'totalsUnder')
        ]) ?>

        <?= $this->render('_stat', [
            'title' => 'Total Under - Main',
            'stats' => TournamentHelper::getOddStat($events, 'totalsUnder', -1)
        ]) ?>

        <?= $this->render('_stat', [
            'title' => 'Total Under - Qualifiers',
            'stats' => TournamentHelper::getOddStat($events, 'totalsUnder', 1)
        ]) ?>

    </div>
</div>
