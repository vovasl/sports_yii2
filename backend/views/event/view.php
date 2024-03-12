<?php

/**
 * @var View $this
 * @var Event $event
 * @var array $statistic
 * @var array $history
 */

use yii\web\View;
use frontend\models\sport\Event;

$this->title = $event->fullName;

$this->render('_breadcrumbs', [
    'model' => $event
]);

?>

<h4><?= $event->formatStartAt . ' ' . $event->tournamentRound->name . ' ' . $event->fullNameLink .' ' . $event->result ?></h4>

<?php if(count($event->odds) > 0): ?>
    <?= $this->render('view/_games', ['event' => $event]); ?>
    <?= $this->render('view/_teams', ['event' => $event]); ?>
    <?= $this->render('view/_sets', ['event' => $event]); ?>
<?php endif; ?>

<?php foreach ($statistic as $type => $dataType) { ?>
    <?php foreach ($dataType as $addType => $dataAddType) { ?>
        <h2 class="mt-5 text-center"><?= ucfirst($type) . ' ' . ucfirst($addType); ?></h2>
        <?php foreach ($dataAddType as $data) { ?>
            <?= $this->render('view/_stats', [
                'data' => $data,
            ]); ?>
        <?php } ?>
    <?php } ?>
<?php } ?>

<?php if(count($history) > 0) { ?>
    <?= $this->render('view/_odd_move', [
        'event' => $event,
        'history' => $history,
    ]); ?>
<?php } ?>
