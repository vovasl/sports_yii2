<?php

namespace frontend\models\sport\query;

use frontend\models\sport\Tournament;
use yii\db\ActiveRecord;

/**
 * This is the ActiveQuery class for [[\frontend\models\sport\Tournament]].
 *
 * @see \frontend\models\sport\Tournament
 */
class TournamentQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Tournament[]|array
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
