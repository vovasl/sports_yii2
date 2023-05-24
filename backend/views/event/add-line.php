<?php

/**
 * @var yii\web\View $this
 * @var array $log
 * @var array $event
 */

use backend\components\pinnacle\helpers\BaseHelper;

?>

<?php BaseHelper::outputArray($event); ?>

<?php BaseHelper::outputArray($log['odds']); ?>