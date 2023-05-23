<?php

/**
 * @var yii\web\View $this
 * @var Tournament[] $tournaments
 */

use frontend\models\sport\Tournament;
use yii\helpers\Url;

$this->title = 'Tournaments';

$this->params['breadcrumbs'][] = $this->title;

?>

<h1><?= $this->title ?></h1>

<div class="container header">
    <div class="row">
        <div class="header-col col-2">Tour</div>
        <div class="header-col col-3">Tournament</div>
        <div class="header-col col-2">Surface</div>
        <div class="header-col col-1">Events</div>
        <div class="header-col col-1">Qual</div>
    </div>
</div>

<?php foreach ($tournaments as $tournament): ?>
    <div class="container">
        <div class="row">
            <div class="col-2"><?= $tournament->tournamentTour->name ?></div>
            <div class="col-3">
                <a href="<?= Url::to(['/tournament/index', 'id' => $tournament->id]) ?>"><?= $tournament->name; ?></a>
            </div>
            <div class="col-2 text-center"><?= $tournament->tournamentSurface->name ?></div>
            <div class="col-1 text-center"><?= count($tournament->events); ?></div>
            <div class="col-1 text-center"><?= $tournament->qualification; ?></div>
        </div>
    </div>
<?php endforeach; ?>