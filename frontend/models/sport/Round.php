<?php

namespace frontend\models\sport;

use Yii;
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

    const QUALIFIER = 5;
    const QUALIFIER_FILTER = 100;
    const SOFA_FIELD_QUALIFIER = 'Qualification';

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
    public static function dropdown(): array
    {
        $rounds = self::find()->select(['name', 'id'])->indexBy('id')->orderBy('rank')->column();
        $rounds[self::QUALIFIER_FILTER] = 'No Qualifiers';
        return $rounds;
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
     * @return int|null
     */
    public static function getIdBySofa($value): ?int
    {
        if(preg_match('#qualification#i', $value)) $value = self::SOFA_FIELD_QUALIFIER;
        return ($res = self::findBySofa($value)) ? $res->id : null;
    }

}
