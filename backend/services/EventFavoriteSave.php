<?php

namespace backend\services;

use frontend\models\sport\Event;
use frontend\models\sport\Odd;
use yii\helpers\ArrayHelper;

class EventFavoriteSave
{

    public function init()
    {
        $events = Event::find()
            ->select(['id'])
            ->where(['favorite' => null])
            ->andWhere(['IS NOT', 'pin_id', null])
            ->all()
        ;

        foreach ($events as $event) {
            $moneyline = ArrayHelper::map(Odd::find()
                ->select(['player_id', 'odd'])
                ->where([
                    'event' => $event->id,
                    'type' => Odd::TYPE['moneyline']
                ])
                ->all(), 'player_id', 'odd')
            ;

            /** event without moneyline */
            if(count($moneyline) != 2) continue;

            /** get favorite */
            foreach ($moneyline as $player_id => $odd) {
                if(is_null($event->favorite) || $odd < reset($moneyline)) {
                    $event->favorite = $player_id;
                }
            }

            /** save */
            $event->save(0);
        }
    }

}