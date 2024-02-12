<?php

use common\helpers\statistic\TotalLineHelper;
use common\helpers\TotalHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

$this->title = 'Statistic Line';

?>

<div class="tournament-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <table class="table table-striped table-bordered detail-view mb-0 mt-4 mb-5">
        <thead>
        <tr>
            <td class="text-center"><strong>Base Total</strong></td>
            <?php foreach (TotalHelper::getStatsTitle(TotalHelper::ODDS) as $title): ?>
                <td class="text-center"><strong><?= $title ?></strong></td>
            <?php endforeach; ?>
        </tr>
        </thead>
        <tbody>

        <?php foreach (TotalLineHelper::getItems() as $item) { ?>

            <tr>
                <td colspan="6" class="text-center"><h3><?= $item['title']; ?></h3></td>
            </tr>
            <?php foreach ($item['data'] as $baseTotal => $data) { ?>
                <tr>
                    <td class="text-center"><?= $baseTotal; ?></td>
                    <?php foreach ($data as $val) { ?>
                        <td class="text-center"><?= $val; ?></td>
                    <?php } ?>
                </tr>
            <?php } ?>

        <?php } ?>

        </tbody>
    </table>

</div>