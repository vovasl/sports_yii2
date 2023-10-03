<?php


namespace backend\controllers;

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
     * @param null $tour
     * @param null $surface
     * @param int $qualifier
     * @param int $detail
     * @return string
     */
    public function actionTotal($tour = null, $surface = null, int $qualifier = 0, int $detail = 0): string
    {

        $tournaments = Tournament::find();
        $tournaments->joinWith([
            'events' => function ($q) use($qualifier) {
                if($qualifier == 0) $q->andOnCondition(['!=', 'tn_event.round', Round::QUALIFIER]);
                else if($qualifier == 1) $q->andOnCondition(['=', 'tn_event.round', Round::QUALIFIER]);
                $q->andOnCondition(['five_sets' => 0]);
                //$q->andOnCondition(['=', 'tn_event.round', 4]);
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

        return $this->render('total', [
            'stats' => OddHelper::tournamentsStats($tournaments->all()),
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
        $rounds = [4];
        $values = [20, 20.5];

        $events = Event::find();
        $events->withData();
        $events->joinWith(['odds' => function($q) {
            $q->andOnCondition([
                'type' => 2,
                'add_type' => 'over',
            ]);
            $q->andOnCondition(['<', 'odd', 175]);
            //$q->andOnCondition(['IS NOT', 'profit', NULL]);
            return $q;
        }]);
        $events->where([
            'tour' => 2,
            'surface' => 1,
        ]);
        $events->andWhere(['!=', 'round', 5]);
        $events->andWhere(['IN', 'value', $values]);
        $events->andWhere(['IN', 'round', $rounds]);
        //$events->andWhere(['LIKE', 'start_at', '2023-09-']);
        $events->orderBy([
            'id' => SORT_DESC
        ]);

        return $this->render('test-total', [
            'models' => $events,
        ]);
    }
}