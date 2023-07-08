<?php


use common\helpers\TournamentHelper;
use yii\web\View;
use common\helpers\OddHelper;

/**
 * @var View $this
 * @var string $type
 * @var array $tournaments
 * @var int $detail
 */

$stats = [];
$output = '';

foreach ($tournaments as $tournament) {

    $tournamentStats = OddHelper::getStats(TournamentHelper::getEventsOdds($tournament), $type);
    $stats = OddHelper::generalStats($tournamentStats, $stats);

    if($detail) {
        $output .= $this->render('_row', [
            'title' => $tournament->name,
            'stats' => $tournamentStats
        ]);
    }

}

$output .=  $this->render('_row', [
    'title' => 'General',
    'stats' => $stats
]);

echo $output;

?>
