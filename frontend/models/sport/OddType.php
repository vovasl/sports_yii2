<?php

namespace frontend\models\sport;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "sp_odd_type".
 *
 * @property int $id
 * @property string $name
 *
 * @property Odd[] $odds
 */
class OddType extends ActiveRecord
{

    CONST MONEYLINE = 'moneyline';
    CONST TOTALS = 'totals';
    const SETS_TOTALS = 'setsTotals';
    const TEAM_TOTAL = 'teamTotal';
    const SPREADS = 'spreads';
    const SETS_SPREADS = 'setsSpreads';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'sp_odd_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * Gets query for [[odds]].
     *
     * @return ActiveQuery
     */
    public function getOdds(): ActiveQuery
    {
        return $this->hasMany(Odd::class, ['type' => 'id']);
    }
}
