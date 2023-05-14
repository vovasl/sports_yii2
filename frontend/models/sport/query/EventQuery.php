<?php

namespace frontend\models\sport\query;

use frontend\models\sport\Event;
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
