<?php


use common\helpers\EventHelper;
use common\helpers\TournamentHelper;
use frontend\models\sport\Odd;
use yii\web\View;

/**
 * @var View $this
 * @var array $tournaments
 * @var string $tour
 * @var string $surface
 * @var int $qualifier
 * @var int $detail
 */

?>

<h1>Totals</h1>
<h2><?= TournamentHelper::getTourSurfaceTitle($tour, $surface) ?></h2>
<h3 class="mb-5"><?= EventHelper::getQualifierText($qualifier) ?></h3>

<?php foreach (Odd::ADD_TYPE as $type) : ?>

    <?= $this->render('total/_outer', [
        'type' => $type,
        'tournaments' => $tournaments,
        'detail' => $detail,
    ]) ?>

<?php endforeach; ?>