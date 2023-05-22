<?php

/**
 * @var yii\web\View $this
 * @var Tournament[] $tournaments
 */

use frontend\models\sport\Tournament;
use yii\helpers\Url;

$this->title = 'Tournaments';

?>

<div class="container header">
    <div class="row">
        <div class="col">Tournament</div>
        <div class="col">Events</div>
        <!--<div class="col">Line</div>-->
    </div>
</div>

<?php foreach ($tournaments as $tournament): ?>
    <div class="container">
        <div class="row">
            <div class="col">
                <a href="<?= Url::to(['/tournament/index', 'id' => $tournament->id]) ?>"><?= $tournament->name; ?></a>
            </div>
            <div class="col">
                <?= $tournament->count_events; ?>
            </div>
            <!--<div class="col"></div>-->
        </div>
    </div>
<?php endforeach; ?>