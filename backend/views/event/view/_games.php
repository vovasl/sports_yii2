<?php

/**
 * @var yii\web\View $this
 * @var Event $event
 */

use frontend\models\sport\Event;

?>

<div class="container">
    <div class="row">
        <div class="header-col col-2">Moneyline</div>
        <div class="header-col col-3">Total</div>
        <div class="header-col col-4">Handicap</div>
    </div>
</div>


<div class="container">
    <div class="row">
        <div class="col-2 text-center"><?= $event->moneyline ?></div>
        <div class="col-3 text-center">
            <?= $this->render('_totals', [
                    'methods' => [
                        'over' => 'totalsOver',
                        'under' => 'totalsUnder'
                    ],
                    'event' => $event
            ]) ?>
        </div>
        <div class="col-4 text-center">
            <?= $this->render('_spreads', [
                    'methods' => ['homeSpreads', 'awaySpreads'],
                    'event' => $event,
            ]) ?>
        </div>
    </div>
</div>
