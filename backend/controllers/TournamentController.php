<?php

namespace backend\controllers;


use frontend\models\sport\Tournament;
use yii\filters\AccessControl;
use yii\web\Controller;

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
     * @return string
     */
    public function actionReview(): string
    {
        $tournaments = Tournament::find()
            ->select(['tn_tournament.*', 'count(tn_event.id) count_events'])
            ->joinWith('events', false)
            ->groupBy('tn_tournament.id')
            ->all()
        ;

        return $this->render('review', [
            'tournaments' => $tournaments
        ]);
    }
}