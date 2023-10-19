<?php


use common\helpers\EventFilterHelper;
use common\helpers\OddHelper;
use common\helpers\PlayerHelper;
use yii\web\View;

/**
 * @var View $this
 * @var array $config
 * @var array $strategies
 */

$stats = [];
foreach ($strategies as $strategy) {
    /** get events */
    $models = EventFilterHelper::getTotalOver($config, $strategy);

    $output .= "<h2>{$strategy['title']}</h2>";
    foreach ($models as $model) {

        /** get odd */
        $odd = OddHelper::getOddByVal($model, $strategy['value']);

        $stats['profit'] += $odd->profit;
        $stats['count']++;

        /** event output */
        $output .= $this->render('strategies/_event', [
            'model' => $model,
            'odd' => $odd
        ]);
    }

    /** get players stats */
    $players = PlayerHelper::getStatsByOddVal($models, $strategy['value']);

    $output .= $this->render('strategies/_players', ['players' => $players]);
}

?>

Events: <?= $stats['count']; ?><br>
Profit: <?= $stats['profit']; ?><hr>
<?= $output; ?>
