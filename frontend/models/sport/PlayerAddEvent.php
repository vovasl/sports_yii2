<?php

namespace frontend\models\sport;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tn_player_add_event".
 *
 * @property int $id
 * @property int $player_id
 * @property string|null $date
 * @property int $sofa_id
 *
 * @property PlayerAdd $player
 */
class PlayerAddEvent extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'tn_player_add_event';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['player_id'], 'required'],
            [['player_id', 'sofa_id'], 'integer'],
            [['date'], 'safe'],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => PlayerAdd::class, 'targetAttribute' => ['player_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'player_id' => 'Player ID',
            'date' => 'Date',
            'sofa_id' => 'Sofascore ID',
        ];
    }

    /**
     * Gets query for [[Player]].
     *
     * @return ActiveQuery
     */
    public function getPlayer(): ActiveQuery
    {
        return $this->hasOne(PlayerAdd::class, ['id' => 'player_id']);
    }
}
