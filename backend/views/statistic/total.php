<?php


use common\helpers\EventHelper;
use common\helpers\TournamentHelper;
use frontend\models\sport\Odd;
use yii\web\View;
use common\helpers\OddHelper;

/**
 * @var View $this
 * @var array $odds
 * @var string $tour
 * @var string $surface
 * @var int $qualifier
 */

?>

<h1>Totals</h1>
<h2><?= TournamentHelper::getTourSurfaceTitle($tour, $surface) ?></h2>
<h3 class="mb-5"><?= EventHelper::getQualifierText($qualifier) ?></h3>

<?php foreach (Odd::ADD_TYPE as $type): ?>

    <?= $this->render('total/_outer', [
        'type' => Odd::ADD_TYPE[$type],
        'stats' => OddHelper::getStats($odds, Odd::ADD_TYPE[$type])
    ]) ?>

<?php endforeach; ?>
