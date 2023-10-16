<?php

namespace backend\controllers;


use backend\components\pinnacle\helpers\BaseHelper;
use backend\models\statistic\FilterModel;
use common\helpers\OddHelper;
use frontend\models\sport\Event;
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
     * @return string
     */
    public function actionTotal(): string
    {
        $filter = new FilterModel(\Yii::$app->request->post());

        $events = Event::find();
        $events->select(['tn_event.*', 'sp_odd.id o_id', 'sp_odd.add_type o_add_type', 'sp_odd.profit o_profit', 'sp_odd.odd o_odd']);
        $events->withData();
        $events->joinWith(['odds' => function($q) {
            $q->andOnCondition(['type' => 2]);
            $q->andOnCondition(['IS NOT', 'profit', NULL]);
            return $q;
        }]);
        $events->indexBy('o_id');

        $events = $filter->searchEvents($events);
        $stats = OddHelper::eventsStats($events->all());

        //BaseHelper::outputArray($stats);die;

        return $this->render('total', [
            'stats' => $stats,
            'filter' => $filter,
        ]);
    }

    /**
     * @param null $tour
     * @param null $surface
     * @param int $qualifier
     * @param int $detail
     * @return string
     */
    public function actionTotalTournaments($tour = null, $surface = null, int $qualifier = 0, int $detail = 0): string
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

    /**
     * @return string
     */
    public function actionTestTotal(): string
    {
        /** Challenger Clay R1 */
        $tour = 2;
        $surface = 1;
        $rounds = [4];
        $values = [21];
        $odds = [
            'min' => 175,
            'max' => 185
        ];
        $moneyline = [
            'more' => 1,
            'limit' => 140
        ];

        /** ATP Hard R1-R16 */

        $tour = 1;
        $surface = 2;
        $rounds = [1, 2, 4, 6];
        $values = [21.5];
        $odds = [
            'min' => 175,
            'max' => 185
        ];
        $moneyline = [
            'more' => 1,
            'limit' => 140
        ];


        $events = Event::find();
        $events->withData();
        $events->joinWith(['odds' => function($q) use($odds) {
            $q->andOnCondition([
                'type' => 2,
                'add_type' => 'over',
            ]);
            $q->andOnCondition(['>=', 'odd', $odds['min']]);
            $q->andOnCondition(['<', 'odd', $odds['max']]);
            //$q->andOnCondition(['IS NOT', 'profit', NULL]);
            return $q;
        }]);
        $events->where([
            'tour' => $tour,
            'surface' => $surface,
        ]);
        $events->andWhere(['IN', 'value', $values]);
        $events->andWhere(['IN', 'round', $rounds]);
        //$events->andWhere(['LIKE', 'start_at', '2023-10-']);
        $events->orderBy([
            'id' => SORT_DESC
        ]);

        return $this->render('test-total', [
            'models' => $events,
            'moneyline' => $moneyline,
        ]);
    }
}