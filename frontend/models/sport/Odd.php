<?php

namespace frontend\models\sport;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "sp_odd".
 *
 * @property int $id
 * @property int $event
 * @property int|null $type
 * @property string|null $add_type
 * @property int $player_id
 * @property string $value
 * @property string $odd
 * @property string|null $created_at
 * @property string $profit
 *
 * @property Event $eventOdd
 * @property OddType $oddType
 * @property Player $player
 */
class Odd extends ActiveRecord
{

    /**
     * @return array[]
     */
    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'sp_odd';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['event', 'odd'], 'required'],
            [['event', 'type', 'player_id', 'odd', 'profit'], 'integer'],
            [['created_at'], 'safe'],
            [['add_type', 'value'], 'string', 'max' => 255],
            [['event'], 'exist', 'skipOnError' => true, 'targetClass' => Event::class, 'targetAttribute' => ['event' => 'id']],
            [['type'], 'exist', 'skipOnError' => true, 'targetClass' => OddType::class, 'targetAttribute' => ['type' => 'id']],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'event' => 'Event',
            'type' => 'Type',
            'add_type' => 'Additional type',
            'player_id' => 'Player ID',
            'value' => 'Value',
            'odd' => 'Odd',
            'created_at' => 'Created At',
            'profit' => 'Profit'
        ];
    }

    /**
     * Gets query for [[eventOdd]].
     *
     * @return ActiveQuery
     */
    public function getEventOdd(): ActiveQuery
    {
        return $this->hasOne(Event::class, ['id' => 'event']);
    }

    /**
     * Gets query for [[oddType]].
     *
     * @return ActiveQuery
     */
    public function getOddType(): ActiveQuery
    {
        return $this->hasOne(OddType::class, ['id' => 'type']);
    }

    /**
     * Gets query for [[player]].
     *
     * @return ActiveQuery
     */
    public function getPlayer(): ActiveQuery
    {
        return $this->hasOne(Player::class, ['id' => 'player_id']);
    }

    /**
     * @param $eventId
     * @param $type
     * @param $oddVal
     * @param null $playerId
     * @param null $value
     * @param null $addType
     * @return bool
     */
    public static function create($eventId, $type, $oddVal, $playerId = null, $value = null, $addType = null): bool
    {
        $odd = new static();;
        $odd->event = $eventId;
        $odd->type = $type;
        $odd->add_type = $addType;
        $odd->player_id = $playerId;
        $odd->value = $value === null ? null : (string)$value;
        $odd->odd = self::setOdd($oddVal);
        return $odd->save();
    }

    /**
     * @param $val
     * @return int
     */
    public static function setOdd($val): int
    {
        return \round($val * 100);
    }

    public function getOddVal()
    {
        return $this->odd/100;
    }
}
