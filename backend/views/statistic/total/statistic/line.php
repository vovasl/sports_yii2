<?php

use common\helpers\statistic\BaseHelper;
use common\helpers\TotalHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var string $title
 * @var array $items
 */

$this->title = $title;

$this->params['breadcrumbs'][] = ['label' => 'Statistic Line', 'url' => ['/statistic/total/statistic-line']];
$this->params['breadcrumbs'][] = $title;

?>

<div class="tournament-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <table class="table table-striped table-bordered detail-view mb-0 mt-4 mb-5">
        <thead>
        <tr>
            <td class="text-center"><strong>Base Total</strong></td>
            <?php foreach (BaseHelper::getStatsTitle(TotalHelper::ODDS) as $title): ?>
                <td class="text-center"><strong><?= $title ?></strong></td>
            <?php endforeach; ?>
        </tr>
        </thead>
        <tbody>

        <?php foreach ($items as $item) { ?>

            <tr>
                <td colspan="6" class="text-center"><h3><?= $item['title']; ?></h3></td>
            </tr>
            <?php foreach ($item['data'] as $baseTotal => $data) { ?>
                <tr>
                    <td class="text-center"><?= $baseTotal; ?></td>
                    <?php foreach ($data as $total => $line) { ?>
                        <td class="text-center line-row"><a href="<?= $line['link']; ?>" target="_blank"><?= $line['stat'] . ' ' . $total; ?></a></td>
                    <?php } ?>
                </tr>
            <?php } ?>

        <?php } ?>

        </tbody>
    </table>

</div>