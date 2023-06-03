<?php

/**
 * @var yii\web\View $this
 * @var array $events
 */

use backend\components\pinnacle\helpers\BaseHelper;

$this->title = 'Result Sofascore';

?>

<h1><?= $this->title ?></h1>

<?php foreach ($events as $event): ?>
    <?= "{$event['tournament']['category']['name']} {$event['tournament']['name']}" ?>
    <?= "<br>" ?>
    <?= "{$event['homeTeam']['name']} ({$event['homeTeam']['id']}) - {$event['awayTeam']['name']} ({$event['awayTeam']['id']})" ?>
    <?php BaseHelper::outputArray($event['result']); ?>
    <?= "<hr>" ?>
<?php endforeach; ?>

<?php BaseHelper::outputArray($events); ?>