<?php

use yii\web\View;

/**
 * @var View $this
 * @var array $add
 */

$this->title = 'Check Players';
$this->params['breadcrumbs'][] = $this->title;

?>

<h1><?= $this->title ?></h1>

<?php if(count($add) > 0) echo $this->render('player/_add', ['add' => $add]); ?>