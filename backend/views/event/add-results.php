<?php

/**
 * @var yii\web\View $this
 * @var array $events
 */

use backend\components\sofascore\models\TennisEvent;

$this->title = 'Results';

?>

<h1><?= $this->title ?></h1>

<?php foreach ($events as $event): ?>
    <?= TennisEvent::output($event) ?>
    <?= "<hr>" ?>
<?php endforeach; ?>