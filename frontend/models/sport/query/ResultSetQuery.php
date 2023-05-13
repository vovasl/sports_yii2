<?php

namespace app\models\sport\query;

use app\models\sport\ResultSet;
use yii\db\ActiveRecord;

/**
 * This is the ActiveQuery class for [[\app\models\sport\ResultSet]].
 *
 * @see \app\models\sport\ResultSet
 */
class ResultSetQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ResultSet[]|array
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
