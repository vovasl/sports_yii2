<?php

namespace frontend\models\sport;


use backend\components\pinnacle\helpers\BaseHelper;
use backend\models\AddLineForm;
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

    CONST ADD_TYPE = [
        'over' => 'over',
        'under' => 'under'
    ];

    CONST TYPE = [
        'spreads' => 1,
        'totals' => 2,
        'team_total' => 3,
        'moneyline' => 4,
        'sets_spreads' => 7,
        'sets_totals' => 8
    ];

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
     * @param AddLineForm $model
     * @return bool
     */
    public static function add(AddLineForm $model): bool
    {
        if(empty($model->player_id)) $model->player_id = null;
        if($model->value == '') $model->value = null;
        if(empty($model->add_type)) $model->add_type = null;

        switch ($model->type) {
            case self::TYPE['spreads']:
                $status = self::createSpread($model, self::TYPE['spreads']);
                break;
            case self::TYPE['totals']:
                $status = self::createTotal($model, self::TYPE['totals']);
                break;
            case self::TYPE['moneyline']:
                $status = self::createMoneyline($model, self::TYPE['moneyline']);
                break;
            case self::TYPE['sets_spreads']:
                $status = self::createSpread($model, self::TYPE['sets_spreads']);
                break;
            case self::TYPE['sets_totals']:
                $status = self::createTotal($model, self::TYPE['sets_totals']);
                break;
            default:
                $status = false;
                break;
        }

        return $status;
    }

    /**
     * @param AddLineForm $model
     * @param $type
     * @return bool
     */
    public static function createSpread(AddLineForm $model, $type): bool
    {
        $odds = self::getSpreadValues($model->event_id, $model->value, $model->odd_home, $model->odd_away);
        foreach ($odds as $playerId => $odd) {
            if(!self::create($model->event_id, $type, $odd['odd'], $playerId, $odd['value'])) return false;
        }
        return true;
    }

    /**
     * @param AddLineForm $model
     * @param $type
     * @return bool
     */
    public static function createTotal(AddLineForm $model, $type): bool
    {
        $odds = [
            self::ADD_TYPE['over'] => $model->odd_over,
            self::ADD_TYPE['under'] => $model->odd_under
        ];
        foreach ($odds as $addType => $odd) {
            if(!self::create($model->event_id, $type, $odd, null, $model->value, $addType)) return false;
        }

        return true;
    }

    /**
     * @param AddLineForm $model
     * @param $type
     * @return bool
     */
    public static function createMoneyline(AddLineForm $model, $type): bool
    {
        $event = Event::findOne($model->event_id);
        $odds = [
            $event->home => $model->odd_home,
            $event->away => $model->odd_away
        ];
        foreach ($odds as $playerId => $odd) {
            if(!self::create($model->event_id, $type, $odd, $playerId)) return false;
        }

        return true;
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
        $odd->odd = self::setOdd($oddVal);
        return $odd->save();
    }

    /**
     * @param $eventId
     * @param $value
     * @param $homeOdd
     * @param $awayOdd
     * @return array[]
     */
    public static function getSpreadValues($eventId, $value, $homeOdd, $awayOdd): array
    {
        $event = Event::findOne($eventId);
        $values = [
            $event->home => [
                'value' => $value,
                'odd' => $homeOdd
            ],
            $event->away => [
                'value' => ($value == 0) ? 0 : -$value,
                'odd' => $awayOdd
            ]
        ];

        return $values;
    }

    /**
     * @param $val
     * @return int
     */
    public static function setOdd($val): int
    {
        return \round($val * 100);
    }

    /**
     * @return float
     */
    public function getOddVal(): float
    {
        return $this->odd/100;
    }

    /**
     * @param $profit
     * @param float $val
     * @return string
     */
    public static function getValueProfit($profit, float $val): string
    {
        return ($profit > 0) ? "<span style='background-color: #66FF99;'>{$val}</span>" : $val;
    }
}
