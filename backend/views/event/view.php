<?php

/**
 * @var yii\web\View $this
 * @var Event $event
 */

use common\helpers\TotalHelper;
use frontend\models\sport\Event;
use frontend\models\sport\Odd;

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
    <?= $this->render('view/_total_stats', [
        'title' => 'Total Over Stats',
        'stats' => TotalHelper::getEventPlayersStat($event, Odd::ADD_TYPE['over']),
    ]) ?>
    <?= $this->render('view/_total_stats', [
        'title' => 'Total Under Stats',
        'stats' => TotalHelper::getEventPlayersStat($event, Odd::ADD_TYPE['under']),
    ]) ?>
<?php endif; ?>
