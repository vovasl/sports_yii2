<?php

/**
 * @var yii\web\View $this
 * @var Event $event
 */

use frontend\models\sport\Event;

$this->title = $event->fullName;

$this->render('_breadcrumbs', [
    'model' => $event
]);

?>

<h4><?= $event->formatStartAt . ' ' . $event->tournamentRound->name . ' ' . $event->fullNameLink .' ' . $event->result ?></h4>

<?php if(count($event->odds) > 0): ?>
    <?= $this->render('view/_games', ['event' => $event]) ?>
    <?= $this->render('view/_teams', ['event' => $event]) ?>
    <?= $this->render('view/_sets', ['event' => $event]) ?>
    <?= $this->render('view/_total_over_stats', ['event' => $event]) ?>
<?php endif; ?>
