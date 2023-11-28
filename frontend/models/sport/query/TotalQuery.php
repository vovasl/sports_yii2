<?php

namespace frontend\models\sport\query;


use frontend\models\sport\Odd;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the ActiveQuery class for [[Total]].
 *
 * @see Total
 */
class TotalQuery extends ActiveQuery
{

    /**
     * @return TotalQuery
     */
    public function over(): TotalQuery
    {
        return $this->andWhere(['type' => Odd::ADD_TYPE['over']]);
    }

    /**
     * @return TotalQuery
     */
    public function under(): TotalQuery
    {
        return $this->andWhere(['type' => Odd::ADD_TYPE['under']]);
    }

    /**
     * @param null $db
     * @return array|ActiveRecord[]
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

    /**
     * @param null $db
     * @return array|ActiveRecord|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
