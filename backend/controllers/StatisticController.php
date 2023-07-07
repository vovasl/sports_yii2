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

        $tournaments = Tournament::find();
        $tournaments->joinWith(['events', 'events.odds']);
        if(!is_null($tour)) $tournaments->andWhere(['tour' => $tour]);
        if(!is_null($surface)) $tournaments->andWhere(['surface' => $surface]);

        if($qualifier == -1) $tournaments->andWhere(['!=' , Event::tableName() . '.round', Round::QUALIFIER]);
        else if($qualifier == 1) $tournaments->andWhere(['=' , Event::tableName() . '.round', Round::QUALIFIER]);

        $tournaments->orderBy(['name' => SORT_ASC]);

        return $this->render('total', [
            'tournaments' => $tournaments->all(),
            'tour' => $tour,
            'surface' => $surface,
            'qualifier' => $qualifier
        ]);
    }
}