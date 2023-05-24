<?php

/**
 * @var yii\web\View $this
 * @var array $log
 * @var array $event
 */

use backend\components\pinnacle\helpers\BaseHelper;

$this->title = 'Add Line';
$this->params['breadcrumbs'][] = $this->title;

?>

<h1><?= $this->title ?></h1>

<h4>Line</h4>
<?php BaseHelper::outputArray($event); ?>

<h4>Odds Log</h4>
<?php BaseHelper::outputArray($log['odds']); ?>