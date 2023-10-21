<?php

namespace backend\controllers;


use backend\components\pinnacle\helpers\BaseHelper;
use backend\models\statistic\FilterModel;
use backend\strategies\Total;
use common\helpers\EventFilterHelper;
use common\helpers\OddHelper;
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

    /**
     * @param int|null $status
     * @return string
     */
    public function actionStrategies(int $status = null): string
    {
        $config = [
            'status' => ($status) ?: EventFilterHelper::EVENTS_STATUS['FINISHED'],
            'add_type' => Odd::ADD_TYPE['over'],
            //'month' => '2023-10-',
        ];

        $strategies = [
            //Total::ATPHardOver(),
            Total::challengerClayOver(),
            //Total::ATPHardOverTest(),
            //Total::challengerClayOverTest()
        ];

        return $this->render('strategies', [
            'config' => $config,
            'strategies' => $strategies,
        ]);

    }
}