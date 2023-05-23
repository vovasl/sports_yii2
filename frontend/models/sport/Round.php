<?php

namespace frontend\models\sport;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "tn_round".
 *
 * @property int $id
 * @property string $name
 * @property string $rank
 *
 * @property Event[] $events
 */
class Round extends \yii\db\ActiveRecord
{

    CONST QUALIFICATION = 5;

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
            [['name'], 'string', 'max' => 255],
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
            'rank' => 'Rank'
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
}
