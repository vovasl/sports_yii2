<?php


namespace backend\controllers;

use frontend\models\sport\Event;
use frontend\models\sport\Round;
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
    public function actionTotal($tour, $surface, $qualifier = 0): string
    {

        $tournaments = Tournament::find()
            ->with(['events.totalsUnder', 'events.totalsOver'])
            ->joinWith(['events'])
            ->where([
                'tour' => $tour,
                'surface' => $surface,
            ])
            ->orderBy([
                'name' => SORT_ASC
            ])
        ;

        if($qualifier == -1) {
            $tournaments->andWhere(['!=' , Event::tableName() . '.round', Round::QUALIFIER]);
        }
        else if($qualifier == 1) {
            $tournaments->andWhere(['=' , Event::tableName() . '.round', Round::QUALIFIER]);
        }


        return $this->render('total', [
            'tournaments' => $tournaments->all(),
            'qualifier' => $qualifier,
        ]);
    }

}