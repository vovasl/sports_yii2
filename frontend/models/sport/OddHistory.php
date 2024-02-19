<?php

namespace frontend\models\sport;


use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "sp_odd_history".
 *
 * @property int $id
 * @property int $event
 * @property int|null $type
 * @property string|null $add_type
 * @property int|null $player_id
 * @property string $value
 * @property string $odd
 * @property string|null $created_at
 *
 * @property Event $eventOdd
 * @property Player $player
 * @property OddType $typeOdd
 */
class OddHistory extends ActiveRecord
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
        return 'sp_odd_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['event', 'odd'], 'required'],
            [['event', 'type', 'player_id', 'odd'], 'integer'],
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
            'add_type' => 'Add Type',
            'player_id' => 'Player ID',
            'value' => 'Value',
            'odd' => 'Odd',
            'created_at' => 'Created At',
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
     * Gets query for [[Player]].
     *
     * @return ActiveQuery
     */
    public function getPlayer(): ActiveQuery
    {
        return $this->hasOne(Player::class, ['id' => 'player_id']);
    }

    /**
     * Gets query for [[typeOdd]].
     *
     * @return ActiveQuery
     */
    public function getTypeOdd(): ActiveQuery
    {
        return $this->hasOne(OddType::class, ['id' => 'type']);
    }

    /**
     * @return float
     */
    public function getOddVal(): float
    {
        return $this->odd/100;
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
        $odd = new static();
        $odd->event = $eventId;
        $odd->type = $type;
        $odd->add_type = $addType;
        $odd->player_id = $playerId;
        $odd->value = $value === null ? null : (string)$value;
        $odd->odd = Odd::setOdd($oddVal);
        return $odd->save();
    }

    /**
     * @param Event $event
     * @return array
     */
    public static function getEventData(Event $event): array
    {
        $data = [];
        foreach (['home', 'away'] as $val) {

            $data[$val] = OddHistory::find()
                ->where([
                    'event' => $event->id,
                    'player_id' => $event->{$val}
                ])
                ->orderBy(['created_at' => SORT_ASC])
                ->all();
        }

        return (count($data['home']) == 0 || count($data['away']) == 0) ? [] : $data;
    }
}
