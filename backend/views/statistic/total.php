<?php


use common\helpers\EventHelper;
use common\helpers\TournamentHelper;
use common\helpers\OddHelper;
use frontend\models\sport\Odd;
use yii\web\View;

/**
 * @var View $this
 * @var array $tournaments
 * @var string $tour
 * @var string $surface
 * @var int $qualifier
 */

?>

<h1>Totals</h1>
<h2><?= TournamentHelper::getTourSurfaceTitle($tour, $surface) ?></h2>
<h3 class="mb-5"><?= EventHelper::getQualifierText($qualifier) ?></h3>

<?php foreach (Odd::ADD_TYPE as $type) : ?>

    <h3><?= ucfirst($type); ?></h3>

    <table class="table table-striped table-bordered detail-view mb-0 mt-3 mb-5">

        <thead>
        <tr>
            <td></td>
            <?php foreach (OddHelper::getStatsTitle(OddHelper::totalSettings()) as $title): ?>
                <td class="text-center"><strong><?= $title ?></strong></td>
            <?php endforeach; ?>
        </tr>
        </thead>
        <tbody>

        <?= $this->render('total/_outer', [
            'type' => $type,
            'tournaments' => $tournaments,
        ]) ?>

        </tbody>
    </table>

<?php endforeach; ?>