<?php


use yii\web\View;
use common\helpers\OddHelper;

/**
 * @var View $this
 * @var string $type
 * @var array $tournaments
 * @var int $detail
 */

?>

<h3><?= ucfirst($type); ?></h3>

<table class="table table-striped table-bordered detail-view mb-0 mt-3 mb-5">
    <thead>
    <tr>
        <td></td>
        <?php foreach (OddHelper::getStatsTitle(OddHelper::totalSettings()) as $title): ?>
            <td class="text-center"><strong><?= $title ?></strong></td>
        <?php endforeach; ?>
    </tr>
    </thead>
    <tbody>
        <?= $this->render('_body', [
            'tournaments' => $tournaments,
            'type' => $type,
            'detail' => $detail,
        ]) ?>
    </tbody>

</table>