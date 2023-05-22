<?php

/**
 * @var yii\web\View $this
 * @var Event $event
 */

use frontend\models\sport\Event;

?>

<div class="container pt-5">
    <div class="row">
        <div class="header-col col-3 text-center"><?= $event->homePlayer->name ?></div>
        <div class="header-col col-3 text-center"><?= $event->awayPlayer->name ?></div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="header-col col-3 text-center">Total</div>
        <div class="header-col col-3 text-center">Total</div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-3 text-center">
            <?= $this->render('_totals', [
                'methods' => [
                    'over' => 'homeTotalsOver',
                    'under' => 'homeTotalsUnder'
                ],
                'event' => $event
            ]) ?>
        </div>
        <div class="col-3 text-center">
            <?= $this->render('_totals', [
                'methods' => [
                    'over' => 'awayTotalsOver',
                    'under' => 'awayTotalsUnder'
                ],
                'event' => $event
            ]) ?>
        </div>
    </div>
</div>
