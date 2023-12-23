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

    CONST ATP = 1;
    CONST CHALLENGER = 2;
    CONST DAVIS_CUP = 3;
    CONST SOFA_CHALLENGER = 72;
    CONST ADD_FILTER = [
        '-1' => 'ATP + DAVIS CUP'
    ];

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
     * @param $tour
     * @return int[]
     */
    public static function filterValue($tour)
    {
        if(empty(self::ADD_FILTER[$tour])) return $tour;
        switch ($tour) {
            case '-1':
                return [self::ATP, self::DAVIS_CUP];
            default:
                return $tour;
        }
    }

}
