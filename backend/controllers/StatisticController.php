<?php

namespace backend\controllers;


use common\helpers\OddHelper;
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
    public function actionTotalTournaments($tour = null, $surface = null, int $qualifier = 0, int $detail = 1): string
    {

        $tournaments = Tournament::find();
        $tournaments->joinWith([
            'events' => function ($q) use($qualifier) {
                if($qualifier) {
                    if ($qualifier == Round::QUALIFIER_FILTER) {
                        $q->andOnCondition(['!=', 'tn_event.round', Round::QUALIFIER]);
                    } else {
                        $q->andOnCondition(['tn_event.round' => $qualifier]);
                    }
                }
                $q->andOnCondition(['five_sets' => 0]);
                //$q->andOnCondition(['tn_event.tournament' => 148]);
                return $q;
            },
            'events.odds' => function($q) {
                $q->andOnCondition(['type' => 2]);
                $q->andOnCondition(['IS NOT', 'profit', NULL]);
                //$q->andOnCondition(['<=', 'value', 21]);
                return $q;
            }
        ]);
        if(!is_null($tour)) $tournaments->andWhere(['tour' => $tour]);
        if(!is_null($surface)) $tournaments->andWhere(['surface' => $surface]);
        $tournaments->orderBy(['name' => SORT_ASC]);

        $stats = OddHelper::tournamentsStats($tournaments->all());
        //BaseHelper::outputArray($stats);die;

        return $this->render('total-tournaments', [
            'stats' => $stats,
            'tour' => $tour,
            'surface' => $surface,
            'qualifier' => $qualifier,
            'detail' => $detail
        ]);
    }

}