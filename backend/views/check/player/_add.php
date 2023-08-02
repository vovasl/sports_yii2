<?php

use yii\web\View;

/**
 * @var View $this
 * @var array $add
 */

?>

<h3 class="mt-5">Add Player</h3>

<table class="table table-striped table-bordered detail-view mb-0 mt-3 mb-5">
    <thead>
    <tr>
        <td class="text-center"><strong>Add Player</strong></td>
        <td class="text-center"><strong>Player</strong></td>
        <td class="text-center"><strong>Date of events</strong></td>
    </tr>
    </thead>
    <tbody>
    <? foreach ($add as $val) : ?>
        <tr>
            <td class="text-center"><?= $val['player_add'] ?></td>
            <td class="text-center"><?= $val['player'] ?></td>
            <td class="text-center">
                <? foreach ($val['player_add_events'] as $event) : ?>
                    <?= date('Y-m-d', strtotime($event->date)) ?>
                    <br>
                <? endforeach; ?>
            </td>
        </tr>
    <? endforeach; ?>
    </tbody>

</table>