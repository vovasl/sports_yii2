<?php

/**
 * @var View $this
 * @var Event $event
 * @var array $odds
 * @var array $log
 */

use yii\web\View;
use backend\components\pinnacle\helpers\BaseHelper;
use frontend\models\sport\Event;

$this->title = 'Add Line';
$this->params['breadcrumbs'][] = $this->title;

?>

<h1><?= $this->title ?></h1>
<h4><?= $event->formatStartAt . ' ' . $event->tournamentRound->name . ' ' . $event->fullNameLink .' ' . $event->result ?></h4>

<?php BaseHelper::outputArray($odds); ?>

<!-- <h4>Odds Log</h4> -->
<?php //BaseHelper::outputArray($log['odds']); ?>