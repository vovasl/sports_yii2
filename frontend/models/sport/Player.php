<?php

namespace frontend\models\sport;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "tn_player".
 *
 * @property int $id
 * @property int|null $type
 * @property string $name
 * @property string|null $birthday
 * @property string|null $plays
 * @property string|null $comment
 *
 * @property Event[] $awayEvents
 * @property Event[] $homeEvents
 * @property Event[] $winnerEvents
 * @property PlayerType $playerType
 */
class Player extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'tn_player';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type'], 'integer'],
            [['name'], 'required'],
            [['birthday'], 'safe'],
            [['comment'], 'string'],
            [['name', 'plays'], 'string', 'max' => 255],
            [['type'], 'exist', 'skipOnError' => true, 'targetClass' => PlayerType::class, 'targetAttribute' => ['type' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'name' => 'Name',
            'birthday' => 'Birthday',
            'plays' => 'Plays',
            'comment' => 'Comment',
        ];
    }

    /**
     * Gets query for [[awayEvents]].
     *
     * @return ActiveQuery
     */
    public function getAwayEvents(): ActiveQuery
    {
        return $this->hasMany(Event::class, ['away' => 'id']);
    }

    /**
     * Gets query for [[homeEvents]].
     *
     * @return ActiveQuery
     */
    public function getHomeEvents(): ActiveQuery
    {
        return $this->hasMany(Event::class, ['home' => 'id']);
    }

    /**
     * Gets query for [[winnerEvents]].
     *
     * @return ActiveQuery
     */
    public function getWinnerEvents(): ActiveQuery
    {
        return $this->hasMany(Event::class, ['winner' => 'id']);
    }

    /**
     * Gets query for [[playerType]].
     *
     * @return ActiveQuery
     */
    public function getPlayerType(): ActiveQuery
    {
        return $this->hasOne(PlayerType::class, ['id' => 'type']);
    }
}
