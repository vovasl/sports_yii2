<?php

/**
 * @var View $this
 * @var string $content
 */

use backend\assets\AppAsset;
use common\widgets\Alert;
use yii\bootstrap4\Breadcrumbs;
use yii\bootstrap4\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use kartik\icons\Icon;
use yii\web\View;

AppAsset::register($this);
Icon::map($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header>
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar navbar-expand-md navbar-dark bg-dark fixed-top',
        ],
    ]);
    $menuItems = [
        ['label' => 'Tournaments', 'url' => ['/tournament']],
        ['label' => 'Events', 'items' => [
            ['label' => 'Moneyline', 'url' => ['/event']],
            ['label' => 'Total', 'url' => ['total/events']],
        ]],
        ['label' => 'Actions', 'items' => [
            ['label' => 'Add Odds', 'url' => ['/event/add-odds']],
            ['label' => 'Add Line', 'url' => ['/event/add-line']],
            ['label' => 'Add Result', 'url' => ['/event/add-result']],
            ['label' => 'Add Results', 'url' => ['/event/add-results']],
        ]],
        ['label' => 'Check', 'items' => [
            ['label' => 'Events', 'url' => ['check/event']],
            ['label' => 'Player', 'url' => ['check/player']],
        ]],
        ['label' => 'Players', 'items' => [
            ['label' => 'Players', 'url' => ['players/player/index']],
            ['label' => 'Statistic', 'url' => ['players/statistic/index']],
        ]
        ],
        ['label' => 'Stats', 'items' => [
            ['label' => 'Totals', 'url' => ['statistic/total']],
        ]],
        ['label' => 'Strategies', 'url' => ['statistic/strategies']],
        ['label' => 'Events Over', 'url' => ['total/events-over']],
        ['label' => 'Frontend', 'url' => 'http://pin2.loc/'],
    ];
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
    } else {
        $menuItems[] = '<li>'
            . Html::beginForm(['/site/logout'], 'post', ['class' => 'form-inline'])
            . Html::submitButton(
                'Logout (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>';
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>
</header>

<main role="main" class="flex-shrink-0">
    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer class="footer mt-auto py-3 text-muted">
    <div class="container">
        <p class="float-left">&copy; <?= Html::encode(Yii::$app->name) ?> <?= date('Y') ?></p>
        <p class="float-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage();
