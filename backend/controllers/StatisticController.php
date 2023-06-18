<?php


namespace backend\controllers;


use frontend\models\sport\Event;
use frontend\models\sport\Odd;
use frontend\models\sport\Round;
use frontend\models\sport\Tournament;
use yii\filters\AccessControl;
use yii\web\Controller;

class StatisticController extends Controller
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
     * @param null $tour
     * @param null $surface
     * @param int $qualifier
     * @return string
     */
    public function actionTotal($tour = null, $surface = null, int $qualifier = 0): string
    {
        $odds = Odd::find();
        $odds->joinWith(['eventOdd', 'eventOdd.eventTournament']);
        $odds->where(['type' => 2]);
        $odds->andWhere(['IS NOT', 'profit', NULL]);

        if(!is_null($tour)) $odds->andWhere([Tournament::tableName() . '.tour' => $tour]);
        if(!is_null($surface)) $odds->andWhere([Tournament::tableName() . '.surface' => $surface]);
        if($qualifier == -1) $odds->andWhere(['!=' , Event::tableName() . '.round', Round::QUALIFIER]);
        else if($qualifier == 1) $odds->andWhere(['=' , Event::tableName() . '.round', Round::QUALIFIER]);

        return $this->render('total', [
            'odds' => $odds->all(),
            'tour' => $tour,
            'surface' => $surface,
            'qualifier' => $qualifier
        ]);
    }
}