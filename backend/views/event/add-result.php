<?php

/**
 * @var yii\web\View $this
 */

use backend\components\pinnacle\helpers\BaseHelper;

$this->title = 'Add event result';
$this->params['breadcrumbs'][] = $this->title;

?>

<h1><?= $this->title ?></h1>

<?php BaseHelper::outputArray($result); ?>