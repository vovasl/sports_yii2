<?php

namespace backend\controllers;


use frontend\models\sport\Event;
use frontend\models\sport\Tournament;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class TournamentController extends Controller
{

    /**
     * @return array[]
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex(int $id): string
    {

        if (!$tournament = Tournament::findOne($id)) {
            throw new NotFoundHttpException('This tournament does not exist');
        }

        $events = Event::find()
            ->from(['event' => 'tn_event'])
            ->withData()
            ->with('odds')
            ->where(['tournament' => $id])
            ->orderTournament()
            ->all()
        ;

        return $this->render('tournament', [
            'tournament' => $tournament,
            'events' => $events
        ]);
    }

    /**
     * @return string
     */
    public function actionTournaments(): string
    {
        $tournaments = Tournament::find()
            ->select(['tn_tournament.*', 'count(tn_event.id) count_events'])
            ->joinWith(['events'])
            ->groupBy('tn_tournament.id')
            ->having(['>', 'count_events', 0])
            ->all()
        ;

        return $this->render('tournaments', [
            'tournaments' => $tournaments
        ]);
    }

}