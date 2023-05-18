<?php

/**
 * @var yii\web\View $this
 * @var string $method
 * @var Event $event
 */

use frontend\models\sport\Event;
use frontend\models\sport\Odd;

?>

<?php if($event->{$method}): ?>
    <table>
        <?php foreach ($event->{$method} as $odd): ?>
            <tr>
                <?php /** @var Odd $odd */ ?>
                <td style="width: 50px"><?= $odd->value ?></td>
                <td><?= $odd->oddVal ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
