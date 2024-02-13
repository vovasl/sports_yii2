<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * @var array $items
 */

$this->title = 'Statistic Line';

?>

<div class="tournament-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="mt-3">

        <h3><?= Html::a('Over', Url::to(['/statistic/total/statistic-line-over']), ['target'=>'_blank']); ?></h3>

        <h3><?= Html::a('Over Favorite', Url::to(['/statistic/total/statistic-line-over', 'favorite' => 1]), ['target'=>'_blank']); ?></h3>

    </div>

</div>
