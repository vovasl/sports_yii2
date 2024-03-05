<?php

namespace frontend\models\sport;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tn_tour".
 *
 * @property int $id
 * @property string $name
 *
 * @property Tournament[] $tournaments
 */
class Tour extends ActiveRecord
{

    /** tours */
    CONST ATP = 1;
    CONST CHALLENGER = 2;
    CONST DAVIS_CUP = 3;
    CONST WTA = 5;
    CONST UNITED_CUP_WOMEN = 7;
    CONST UNITED_CUP_MEN = 8;

    /** combine tours */
    CONST ATP_ALL = [self::ATP, self::DAVIS_CUP, self::UNITED_CUP_MEN];
    CONST WTA_ALL = [self::WTA, self::UNITED_CUP_WOMEN];

    /** additional filter */
    CONST ADD_FILTER = [
        -1 => 'ATP + DAVIS CUP + United Cup',
        -2 => 'WTA + United Cup'
    ];

    CONST SOFA_CHALLENGER = 72;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'tn_tour';
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
     * Gets query for [[tournaments]].
     *
     * @return ActiveQuery
     */
    public function getTournaments(): ActiveQuery
    {
        return $this->hasMany(Tournament::class, ['tour' => 'id']);
    }

    /**
     * @return array
     */
    public static function dropdown(): array
    {
        return array_replace(self::find()->select(['name', 'id'])->indexBy('id')->column(), self::ADD_FILTER);
    }

    /**
     * @param string|int $tour
     * @return int[]|null|string
     */
    public static function getValue($tour)
    {
        if(empty(self::ADD_FILTER[$tour])) return $tour;
        switch ($tour) {
            case -1:
                return self::ATP_ALL;
            case -2:
                return self::WTA_ALL;
            default:
                return $tour;
        }
    }

    /**
     * @param int|null $tour
     * @return int|null
     */
    public static function getFilterValue(?int $tour): ?int
    {
        if(in_array($tour, Tour::ATP_ALL)) return -1;
        if(in_array($tour, Tour::WTA_ALL)) return -2;

        return $tour;
    }

}
