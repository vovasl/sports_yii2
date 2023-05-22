<?php

/**
 * @var yii\web\View $this
 * @var array $methods
 * @var Event $event
 */

use frontend\models\sport\Event;

?>

<?php foreach ($event->getTotals($methods) as $k => $odd): ?>
    <div class="container">
        <div class="row">
            <div class="col"><?= $k ?></div>
            <div class="col"><?= $odd['over'] ?></div>
            <div class="col"><?= $odd['under'] ?></div>
        </div>
    </div>
<?php endforeach; ?>
