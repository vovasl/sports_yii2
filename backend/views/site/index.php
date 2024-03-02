<?php

use yii\helpers\Url;
use yii\web\View;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/**
 * @var View $this
 * @var array $settings
 * @var ActiveDataProvider[] $dataProvider
 */

$this->title = 'Tennis Odds';

?>

<div class="tournament-index">

    <?php foreach ($settings as $k => $setting) { ?>

        <h3><?= Html::a(Html::encode($setting['title']), Url::to($setting['url'])); ?></h3>

        <?= $this->render('events/grid', [
            'searchModel' => $setting['search_model'],
            'dataProvider' => $dataProvider[$k],
        ]); ?>

    <?php } ?>

</div>
