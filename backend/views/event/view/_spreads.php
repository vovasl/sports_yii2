<?php

/**
 * @var yii\web\View $this
 * @var array $methods
 * @var Event $event
 */

use frontend\models\sport\Event;
use frontend\models\sport\Odd;

?>

<div class="container">
    <div class="row">
        <?php foreach ($methods as $method): ?>
            <div class="col">
                <?php foreach ($event->{$method} as $odd): ?>
                    <div class="container">
                        <div class="row">
                            <div class="col"><?= $odd->value ?></div>
                            <div class="col"><?= Odd::getValueProfit($odd->profit, $odd->oddVal) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>