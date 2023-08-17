<?php

namespace frontend\models\sport;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;

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

    /**
     * @param array $data
     * @param string $field
     */
    public static function add(array $data, string $field)
    {
        /** get player */
        $player = ($player = PlayerAdd::findOne(['name' => $data[$field]['name']])) ? $player : new PlayerAdd();
        if($player->isNewRecord) {
            $player->name = trim($data[$field]['name']);
            $player->save();
        }

        /** save event */
        $event = new PlayerAddEvent();
        $event->player_id = $player->id;
        $event->date = date('Y-m-d', $data['startTimestamp']);
        $event->sofa_id = $data['id'];
        $event->save();
    }

    /**
     * @param $id
     * @return bool
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public static function removeBySofa($id): bool
    {
        /** get events */
        $events = self::find()->where(['sofa_id' => $id])->all();
        foreach ($events as $event) {

            /** remove event */
            $event->delete();

            /** remove player without events */
            if(self::find()->where(['player_id' => $event->player_id])->count() < 1) {
                PlayerAdd::deleteAll(['id' => $event->player_id]);
            }
        }

        return true;
    }

}
