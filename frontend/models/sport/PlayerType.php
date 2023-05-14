<?php

namespace frontend\models\sport;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "tn_player_type".
 *
 * @property int $id
 * @property string $name
 *
 * @property Player[] $players
 */
class PlayerType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'tn_player_type';
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
     * Gets query for [[players]].
     *
     * @return ActiveQuery
     */
    public function getPlayers(): ActiveQuery
    {
        return $this->hasMany(Player::class, ['type' => 'id']);
    }
}
