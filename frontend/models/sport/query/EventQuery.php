<?php

namespace frontend\models\sport\query;


use frontend\models\sport\Event;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the ActiveQuery class for [[\frontend\models\sport\Event]].
 *
 * @see \frontend\models\sport\Event
 */
class EventQuery extends \yii\db\ActiveQuery
{

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

    /**
     * @return EventQuery
     */
    public function withData(): EventQuery
    {
        return $this
            ->with(['playerHome', 'playerAway'])
            ->joinWith(['eventTournament', 'tournamentRound', 'eventTournament.tournamentTour'])
            ;
    }

    /**
     * @return EventQuery
     */
    public function order(): EventQuery
    {
        return $this->orderBy([
            'tn_tour.name' => SORT_ASC,
            'tn_tournament.name' => SORT_ASC,
            'tn_round.rank' => SORT_ASC,
            'event.start_at' => SORT_ASC,
        ]);
    }

    /**
     * @return EventQuery
     */
    public function notStarted(): EventQuery
    {
        return $this->andWhere(['>','event.start_at', new Expression('NOW()')]);
    }

}
