<?php


namespace backend\controllers;

use frontend\models\sport\Tournament;
use yii\filters\AccessControl;
use yii\web\Controller;

class TournamentStatisticController extends Controller
{

    /**
     * @inheritDoc
     */
    public function behaviors(): array
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * @return string
     */
    public function actionTotal(): string
    {

        $tour = 2;
        $surface = 1;
        $type = 'Under';
        $qualifier = -1;

        $tournaments = Tournament::find()
            ->with(['events', 'events.totalsUnder'])
            ->where([
                'tour' => $tour,
                'surface' => $surface,
            ])
            ->orderBy([
                'name' => SORT_ASC
            ])
            ->all();
        ;
        return $this->render('total', [
            'tournaments' => $tournaments,
            'type' => $type,
            'qualifier' => $qualifier,
        ]);
    }

}