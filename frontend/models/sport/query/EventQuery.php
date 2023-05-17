<?php

namespace frontend\models\sport\query;

use frontend\models\sport\Event;
use frontend\models\sport\Odd;
use frontend\models\sport\OddType;
use yii\db\ActiveRecord;

/**
 * This is the ActiveQuery class for [[\frontend\models\sport\Event]].
 *
 * @see \frontend\models\sport\Event
 */
class EventQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @return EventQuery
     */
    public function withData(): EventQuery
    {
        return $this
            ->with(['playerHome', 'playerAway', 'eventTournament.tournamentTour'])
            ->joinWith(['eventTournament', 'tournamentRound'])
        ;
    }

    public function order()
    {
        return $this->orderBy([
            'tn_tournament.name' => SORT_ASC,
            'tn_round.name' => SORT_ASC,
            'event.start_at' => SORT_ASC,
        ]);
    }

    /**
     * {@inheritdoc}
     * @return Event[]|array
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return array|ActiveRecord|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
