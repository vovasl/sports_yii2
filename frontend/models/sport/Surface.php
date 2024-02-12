<?php

namespace frontend\models\sport;


use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tn_surface".
 *
 * @property int $id
 * @property string $name
 *
 * @property Tournament[] $tournaments
 */
class Surface extends ActiveRecord
{

    CONST SURFACES = [
        'clay' => 1,
        'hard' => 2,
        'indoor' => 4,
    ];
    CONST ADD_FILTER = [
        '-1' => 'Hard + Indoor'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'tn_surface';
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
        return $this->hasMany(Tournament::class, ['surface' => 'id']);
    }

    /**
     * @return array
     */
    public static function dropdown(): array
    {
        return array_replace(self::find()->select(['name', 'id'])->indexBy('id')->column(), self::ADD_FILTER);
    }

    /**
     * @param $surface
     * @return int[]
     */
    public static function filterValue($surface)
    {
        if(empty(self::ADD_FILTER[$surface])) return $surface;
        switch ($surface) {
            case '-1':
                return [self::SURFACES['hard'], self::SURFACES['indoor']];
            default:
                return $surface;
        }
    }

}
