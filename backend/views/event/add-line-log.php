<?php

/**
 * @var View $this
 * @var Event $event
 * @var array $odds
 * @var array $log
 * @var bool $save
 */

use yii\web\View;
use backend\components\pinnacle\helpers\BaseHelper;
use frontend\models\sport\Event;
use yii\helpers\Html;
use yii\web\JqueryAsset;

$this->title = 'Add Line';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('/js/add-line-log.js', ['depends' => [JqueryAsset::class]]);

?>

<h1><?= $this->title ?></h1>

<h4><?= $event->formatStartAt . ' ' . $event->tournamentRound->name . ' ' . $event->fullNameLink .' ' . $event->result ?></h4>

<?php if($save): ?>

    <?= $this->render('add-line-log/_form'); ?>

<?php endif; ?>

<?= Html::a('Show Full Log', [], ['class' => 'btn btn-primary mb-3 full-log-btn']) ?>

<?php BaseHelper::outputArray($odds); ?>

<div id="full-log" style="display: none;">

    <h5>Full Log</h5>

    <?php BaseHelper::outputArray($log['odds']); ?>

</div>
