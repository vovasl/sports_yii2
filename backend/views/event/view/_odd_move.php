<?php

/**
 * @var yii\web\View $this
 * @var Event $event
 */

use frontend\models\sport\Event;
use frontend\models\sport\OddHistory;

foreach (['home', 'away'] as $val) {

    $history[$val] = OddHistory::find()
        ->where([
            'event' => $event->id,
            'player_id' => $event->{$val}
        ])
        ->orderBy(['created_at' => SORT_ASC])
        ->all();
}

?>

<h2 class="mt-5 mb-5 text-center w-50">Odd Move Moneyline</h2>

<div class="col-xs-6 w-25 float-left">
    <h5 class="sub-header text-center"><?= $event->homePlayer->name; ?></h5>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th class="col-md-1">#</th>
                <th class="col-md-2 text-center">Time</th>
                <th class="col-md-1 text-right">Odd</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($history['home'] as $k => $model): ?>
                <tr>
                    <td class="col-md-1"><?= $k; ?></td>
                    <td class="col-md-2"><?= $model->created_at; ?></td>
                    <td class="col-md-1 text-right"><?= $model->oddVal; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="col-xs-6 w-25 float-left">
    <h5 class="sub-header text-center"><?= $event->awayPlayer->name; ?></h5>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th class="col-md-1">Odd</th>
                <th class="col-md-2 text-center">Time</th>
                <th class="col-md-1">#</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($history['away'] as $k => $model): ?>
                <tr>
                    <td class="col-md-1"><?= $model->oddVal; ?></td>
                    <td class="col-md-2"><?= $model->created_at; ?></td>
                    <td class="col-md-1"><?= $k; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>