<?php

namespace console\controllers;


use frontend\models\sport\Event;
use frontend\models\sport\Total;
use Yii;
use yii\console\Controller;

class TotalController extends Controller
{

    public function actionAdd()
    {
        Yii::$app->db->createCommand()->truncateTable(Total::tableName())->execute();
        $events = Event::find()
            ->active()
            ->joinWith([
                'homeMoneyline',
                'awayMoneyline',
                'totalsOver',
                'totalsUnder',
                'eventTournament',
                'eventTournament.tournamentTour',
                'eventTournament.tournamentSurface'
            ])
            //->limit(1)
            ->all()
        ;
        foreach ($events as $event) {
            $total = new Total();
            $total->tour_id = $event->eventTournament->tournamentTour->id;
            $total->surface_id = $event->eventTournament->tournamentSurface->id;
            $total->event_id = $event->id;
            $total->save(0);
        }
    }
}