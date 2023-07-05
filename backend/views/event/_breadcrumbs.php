<?php


use frontend\models\sport\Event;
use yii\web\View;

/**
 * @var View $this
 * @var Event $model
 */

$this->params['breadcrumbs'][] = ['label' => 'Tournaments', 'url' => ['/tournament']];
$this->params['breadcrumbs'][] = ['label' => $model->eventTournament->name, 'url' => ["/tournament/{$model->tournament}"]];
$this->params['breadcrumbs'][] = ['label' => 'Events', 'url' => ["/tournament/{$model->tournament}/event"]];
$this->params['breadcrumbs'][] = $model->fullName;

?>