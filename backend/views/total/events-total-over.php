<?php

use backend\helpers\EventResultSaveHelper;
use backend\models\total\PlayerTotalSearch;
use yii\web\View;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/**
 * @var View $this
 * @var PlayerTotalSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 */

$this->title = 'Events Total Over';

$playerTotal= \frontend\models\sport\PlayerTotal::find()->all();

?>

<h1><?= Html::encode($this->title) ?></h1>

<?php

$events = \frontend\models\sport\Event::find()
    ->withData()
    ->joinWith([
        'homeMoneyline',
        'awayMoneyline',
        'totalsOver',
    ])
    ->andWhere(['IN', 'home', \yii\helpers\ArrayHelper::getColumn($playerTotal, 'player_id')])
    ->andWhere(['IN', 'away', \yii\helpers\ArrayHelper::getColumn($playerTotal, 'player_id')])
    ->andWhere(['IN', 'tn_tournament.tour', [1, 3, 8]])
    ->andWhere(['tn_tournament.surface' => 2])
    ->andWhere(['>=', 'home_moneyline.odd', 150])
    ->andWhere(['>=', 'away_moneyline.odd', 150])
    //->andWhere(['five_sets' => 0])
    //->andWhere(['tn_event.sofa_id' => null])
    ->orderBy(['tn_event.id' => SORT_DESC])
    ->all()
;


$profit = $count = 0;
$output = '';
foreach ($events as $event) {
    $oddKey = 0;
    $over = [];
    foreach ($event->totalsOver as $k => $totalOver) {
        //if($totalOver->odd < 176) {
        if($totalOver->odd > 165 && $totalOver->odd <= 180) {
            $over[$k] = $totalOver->profit;
            $oddKey = $k;
        }
    }
    if(count($over) > 1) {
        $oddKey = array_key_last($over);
    }

    $profit += (int)$event->totalsOver[$oddKey]->profit;
    $count++;

    $output .= "{$event->eventTournament->name} ";
    $output .= ", {$event->tournamentRound->name}";
    $output .= "<br>{$event->formatStartAt}";
    $output .= " {$event->fullNameLink} {$event->result}";
    $output .= "<br>" . EventResultSaveHelper::getLink($event->id);
    $output .= "<br>{$event->totalsOver[$oddKey]->oddVal}({$event->totalsOver[$oddKey]->value}): {$event->totalsOver[$oddKey]->profit}";
    $output .= "<hr>";

}

$output .= "Events: {$count}<br>";
$output .= "Profit: $profit";
$output .= "<hr>";
echo $output;

?>

