<?php

namespace backend\controllers;


use frontend\models\sport\Event;
use frontend\models\sport\Round;
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

        return $this->render('index', [
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
            ->select(['tn_tournament.*', 'count(qual.id) qualification'])
            ->with(['tournamentSurface', 'events'])
            ->joinWith(['tournamentTour'])
            ->leftJoin('tn_event qual', 'tn_tournament.id = qual.tournament and qual.round = ' . Round::QUALIFICATION)
            ->orderBy([
                'tn_tour.name' => SORT_ASC,
                'name' => SORT_ASC
            ])
            ->groupBy('tn_tournament.id')
            ->all()
        ;

        return $this->render('tournaments', [
            'tournaments' => $tournaments
        ]);
    }

}