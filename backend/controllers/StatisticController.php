<?php


namespace backend\controllers;

use common\helpers\OddHelper;
use common\helpers\TournamentHelper;
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
     * @param int $detail
     * @return string
     */
    public function actionTotal($tour = null, $surface = null, int $qualifier = -1, int $detail = 1): string
    {

        $tournaments = Tournament::find();
        $tournaments->joinWith([
            'events' => function ($q) use($qualifier) {
                if($qualifier == 0) $q->andOnCondition(['!=', 'tn_event.round', Round::QUALIFIER]);
                else if($qualifier == 1) $q->andOnCondition(['=', 'tn_event.round', Round::QUALIFIER]);
                return $q;
            },
            'events.odds' => function($q) {
                $q->andOnCondition(['type' => 2]);
                $q->andOnCondition(['IS NOT', 'profit', NULL]);
                return $q;
            }
        ]);
        if(!is_null($tour)) $tournaments->andWhere(['tour' => $tour]);
        if(!is_null($surface)) $tournaments->andWhere(['surface' => $surface]);
        $tournaments->orderBy(['name' => SORT_ASC]);

        return $this->render('total', [
            'stats' => OddHelper::tournamentsStats($tournaments->all()),
            'tour' => $tour,
            'surface' => $surface,
            'qualifier' => $qualifier,
            'detail' => $detail
        ]);
    }
}