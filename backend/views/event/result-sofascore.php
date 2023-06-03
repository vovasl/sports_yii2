<?php

/**
 * @var yii\web\View $this
 * @var array $events
 */

use backend\components\pinnacle\helpers\BaseHelper;

$this->title = 'Result Sofascore';

?>

<h1><?= $this->title ?></h1>


<?php BaseHelper::outputArray($events); ?>