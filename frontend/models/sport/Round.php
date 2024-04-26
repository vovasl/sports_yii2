<?php

namespace frontend\models\sport;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tn_round".
 *
 * @property int $id
 * @property string $name
 * @property string $rank
 * @property string $sofa_name
 *
 * @property Event[] $events
 */
class Round extends ActiveRecord
{

    CONST FINAL = 3;
    CONST SF = 8;
    CONST QF = 7;
    CONST QUALIFIER = 5;
    CONST MAIN = 100;

    /** additional filters */
    CONST ADD_FILTER = [
        self::MAIN => 'Main',
        -1 => 'Final-QF'
    ];

    CONST FILTER_MAPPING = [
        100 => 'getMainRounds',
        -1 => [self::FINAL, self::SF, self::QF]
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'tn_round';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name', 'sofa_name'], 'string', 'max' => 255],
            [['rank'], 'integer'],
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
            'rank' => 'Rank',
            'sofa_name' => 'Sofascore field name',
        ];
    }

    /**
     * Gets query for [[events]].
     *
     * @return ActiveQuery
     */
    public function getEvents(): ActiveQuery
    {
        return $this->hasMany(Event::class, ['round' => 'id']);
    }

    /**
     * @return array
     */
    public static function getMainRounds(): array
    {
        return self::find()
            ->select(['id'])
            ->where(['!=', 'id', self::QUALIFIER])
            ->orderBy('rank')
            ->column()
        ;
    }

    /**
     * @return array
     */
    public static function dropdown(): array
    {
        return self::find()->select(['name', 'id'])->indexBy('id')->orderBy('rank')->column();
    }

    /**
     * @return array
     */
    public static function dropdownFilter(): array
    {
        return  array_replace(self::dropdown(), self::ADD_FILTER);
    }

    /**
     * @param string $val
     * @return int|int[]
     */
    public static function filterValue(string $val)
    {
        if (empty(self::FILTER_MAPPING[$val])) return $val;

        $val = self::FILTER_MAPPING[$val];
        return is_array($val) ? $val : self::$val();
    }

    /**
     * @param $value
     * @return Round|null
     */
    public static function findBySofa($value): ?Round
    {
        return self::findOne(['sofa_name' => $value]);
    }

    /**
     * @param $value
     * @param int $tour
     * @return int|null
     */
    public static function getIdBySofa($value, int $tour): ?int
    {
        /** qualifier */
        if(preg_match('#qualification|qualifying#i', $value)) $value = 'Qualification';

        /** R1 for challenger */
        if($value == 'Round of 32' && $tour == Tour::SOFA_CHALLENGER) $value = 'Round of 128';

        return ($res = self::findBySofa($value)) ? $res->id : null;
    }

}
