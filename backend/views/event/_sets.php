<?php

/**
 * @var yii\web\View $this
 * @var Event $event
 */

use frontend\models\sport\Event;

?>

<div class="container pt-5">
    <div class="row">
        <div class="header-col col-7 text-center">Sets</div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="header-col col-3 text-center">Total</div>
        <div class="header-col col-4 text-center">Handicap</div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-3 text-center">
            <?= $this->render('_totals', [
                'methods' => [
                    'over' => 'setsTotalsOver',
                    'under' => 'setsTotalsUnder'
                ],
                'event' => $event
            ]) ?>
        </div>
        <div class="col-4 text-center">
            <?= $this->render('_spreads', [
                'methods' => ['homeSetsSpreads', 'awaySetsSpreads'],
                'event' => $event,
            ]) ?>
        </div>
    </div>
</div>
