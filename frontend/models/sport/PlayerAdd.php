<?php

namespace frontend\models\sport;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tn_player_add".
 *
 * @property int $id
 * @property string $name
 *
 * @property PlayerAddEvent[] $tnPlayerAddEvents
 */
class PlayerAdd extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'tn_player_add';
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
     * Gets query for [[TnPlayerAddEvents]].
     *
     * @return ActiveQuery
     */
    public function getPlayerAddEvents(): ActiveQuery
    {
        return $this->hasMany(PlayerAddEvent::class, ['player_id' => 'id']);
    }
}
