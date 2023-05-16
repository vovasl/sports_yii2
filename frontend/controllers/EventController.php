<?php

namespace frontend\controllers;


use frontend\models\sport\Event;
use yii\web\Controller;

class EventController extends Controller
{

    /**
     * @return string
     */
    public function actionMoneyline()
    {
        $events = Event::find()
            ->from(['event' => 'tn_event'])
            ->with(['eventTournament.tournamentTour', 'playerHome', 'playerAway', 'homeMoneyline', 'awayMoneyline'])
            ->joinWith(['eventTournament', 'tournamentRound'])
            ->groupBy('event.id')
            ->orderBy([
                'tn_tournament.name' => SORT_ASC,
                'tn_round.name' => SORT_ASC,
                'event.start_at' => SORT_ASC
            ])
            ->all()
        ;
       return $this->render('moneyline', [
           'events' => $events
       ]);
    }

}