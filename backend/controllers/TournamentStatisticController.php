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
     * @param null $tour
     * @param null $surface
     * @param int $qualifier
     * @return string
     */
    public function actionTotal($tour = null, $surface = null, int $qualifier = 0): string
    {

        $q = Tournament::find()
            ->select([Tournament::tableName() . '.*', 'count(tn_event.id) count_events'])
            ->with(['events.totalsUnder', 'events.totalsOver'])
            ->joinWith(['events'])
            ->where(['IS NOT', Event::tableName() . '.winner', null])
        ;

        if(!is_null($tour)) $q->andWhere(['tour' => $tour]);
        if(!is_null($surface)) $q->andWhere(['surface' => $surface]);
        if($qualifier == -1) $q->andWhere(['!=' , Event::tableName() . '.round', Round::QUALIFIER]);
        else if($qualifier == 1) $q->andWhere(['=' , Event::tableName() . '.round', Round::QUALIFIER]);

        $q->groupBy([Tournament::tableName() . '.id']);
        $q->orderBy([
            'name' => SORT_ASC
        ]);

        return $this->render('total', [
            'tournaments' => $q->all(),
            'tour' => $tour,
            'surface' => $surface,
            'qualifier' => $qualifier,
        ]);
    }

}