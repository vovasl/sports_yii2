<?php


namespace common\helpers;


use frontend\models\sport\Event;
use yii\helpers\Html;

class EventOutputHelper
{

    /**
     * @param Event $model
     * @param string $value
     * @param $profit
     * @return string
     */
    public static function total(Event $model, string $value, $profit): string
    {
        $output = "";
        $link = Html::a('Link', ['/event/view', 'id' => $model->id], ['target'=>'_blank']);

        $output .= "{$model->fullInfo} {$model->result} <br>";
        $output .= "{$link}<br>";
        //if(count($model->odds) > 1) $output .= "More Odds <br>";
        $output .= "Moneyline: {$model->homeMoneyline[0]->oddVal} - {$model->awayMoneyline[0]->oddVal} <br>";

        $output .= "Total({$value}): {$profit}";
        $output .= "<hr>";

        return $output;
    }

}