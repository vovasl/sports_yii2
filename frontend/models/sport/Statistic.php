<?php

namespace frontend\models\sport;


use backend\models\statistic\total\PlayerTotalSearch;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tn_statistic".
 *
 * @property int $id
 * @property int|null $player_id
 * @property int|null $event_id
 * @property int|null $type
 * @property string|null $add_type
 * @property int|null $min_moneyline
 * @property int|null $profit_0
 * @property int|null $profit_1
 * @property int|null $profit_2
 * @property int|null $profit_3
 * @property int|null $profit_4
 * @property int|null $count_events
 * @property int|null $percent_profit
 * @property int|null $percent_profit_0
 * @property int|null $percent_profit_1
 * @property int|null $percent_profit_2
 * @property int|null $percent_profit_3
 * @property int|null $percent_profit_4
 *
 * @property Player $player
 * @property Event $event
 * @property OddType $oddType
 * @property PlayerTotal[] $playerTotal
 * @property Odd $odd0
 * @property Odd $odd1
 * @property Odd $odd2
 * @property Odd $odd3
 * @property Odd $odd4
 */
class Statistic extends ActiveRecord
{

    CONST TOTAL_FILTER = [
        'moneyline' => [
            'favorite' => '1.45<=',
            'equal' => '1.55>='
        ]
    ];

    public $count_events;
    public $count_profit_0;
    public $count_profit_1;
    public $count_profit_2;
    public $count_profit_3;
    public $count_profit_4;

    public $percent_profit;
    public $percent_profit_0;
    public $percent_profit_1;
    public $percent_profit_2;
    public $percent_profit_3;
    public $percent_profit_4;

    public $date_from;
    public $date_to;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'tn_statistic';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['player_id', 'event_id', 'type', 'odd_id_0', 'odd_id_1', 'odd_id_2', 'odd_id_3', 'odd_id_4', 'count_events', 'count_profit_0', 'count_profit_1', 'count_profit_2', 'count_profit_3', 'count_profit_4', 'profit_0', 'profit_1', 'profit_2', 'profit_3', 'profit_4', 'percent_profit', 'percent_profit_0', 'percent_profit_1', 'percent_profit_2', 'percent_profit_3', 'percent_profit_4'], 'integer'],
            [['add_type', 'min_moneyline', 'date_from', 'date_to'], 'string', 'max' => 255],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => Event::class, 'targetAttribute' => ['event_id' => 'id']],
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
            'player_id' => 'Player ID',
            'event_id' => 'Event ID',
            'type' => 'Type',
            'add_type' => 'Additional Type',
            'min_moneyline' => 'Moneyline',
            'odd_id_0' => 'Odd ID 0',
            'odd_id_1' => 'Odd ID 1',
            'odd_id_2' => 'Odd ID 2',
            'odd_id_3' => 'Odd ID 3',
            'odd_id_4' => 'Odd ID 4',
            'count_events' => 'Events',
            'profit_0' => 'Profit 0',
            'profit_1' => 'Profit 1',
            'profit_2' => 'Profit 2',
            'profit_3' => 'Profit 3',
            'profit_4' => 'Profit 4',
            'date_from' => 'From',
            'date_to' => 'To',
        ];
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
     * Gets query for [[Event]].
     *
     * @return ActiveQuery
     */
    public function getEvent(): ActiveQuery
    {
        return $this->hasOne(Event::class, ['id' => 'event_id']);
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
     * @return ActiveQuery
     */
    public function getPlayerTotal(): ActiveQuery
    {
        return $this->hasMany(PlayerTotal::class, ['player_id' => 'player_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOdd0(): ActiveQuery
    {
        return $this->hasOne(Odd::class, ['id' => 'odd_id_0']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOdd1(): ActiveQuery
    {
        return $this->hasOne(Odd::class, ['id' => 'odd_id_1']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOdd2(): ActiveQuery
    {
        return $this->hasOne(Odd::class, ['id' => 'odd_id_2']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOdd3(): ActiveQuery
    {
        return $this->hasOne(Odd::class, ['id' => 'odd_id_3']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOdd4(): ActiveQuery
    {
        return $this->hasOne(Odd::class, ['id' => 'odd_id_4']);
    }

    /**
     * @return string
     */
    public function getPercentProfit(): string
    {
        return $this->percent_profit ?? 0;
    }

    /**
     * @return string
     */
    public function getPercentProfitOutput(): string
    {
        return ($this->count_events > 0) ? "{$this->getPercentProfitValOutput()}($this->count_events)" : " - ";
    }

    /**
     * @return string
     */
    public function getPercentProfitValOutput(): string
    {
        if($this->count_events == 0) return '';

        $minEvents = 100;
        if ($this->count_events >= $minEvents) {
            $class = ($this->percent_profit >= 0) ? 'success-line' : 'fail-line';
            switch ($this->percent_profit) {
                case ($this->percent_profit >= 2):
                    $class = 'success-line';
                    break;
                case ($this->percent_profit < -2):
                    $class = 'fail-line';
                    break;
                default:
                    $class = 'neutral-line';
                    break;
            }
            $percentProfit = "<span class='" . $class . "'>{$this->percent_profit}%</span>";
        }
        else {
            $percentProfit = "{$this->percent_profit}%";
        }

        return $percentProfit;
    }

    /**
     * @return string
     */
    public function getPercentProfit0(): string
    {
        return ($this->count_profit_0 > 0) ? "{$this->percent_profit_0}%($this->count_profit_0)" : ' - ';
    }

    /**
     * @return string
     */
    public function getPercentProfit1(): string
    {
        return ($this->count_profit_1 > 0) ? "{$this->percent_profit_1}%($this->count_profit_1)" : ' - ';
    }

    /**
     * @return string
     */
    public function getPercentProfit2(): string
    {
        return ($this->count_profit_2 > 0) ? "{$this->percent_profit_2}%($this->count_profit_2)" : ' - ';
    }

    /**
     * @return string
     */
    public function getPercentProfit3(): string
    {
        return ($this->count_profit_3 > 0) ? "{$this->percent_profit_3}%($this->count_profit_3)" : ' - ';
    }

    /**
     * @return string
     */
    public function getPercentProfit4(): string
    {
        return ($this->count_profit_4 > 0) ? "{$this->percent_profit_4}%($this->count_profit_4)" : ' - ';
    }

    /**
     * @param string $type
     * @param PlayerTotalSearch $search
     * @param array $data
     * @return bool
     */
    public function playerTotalButton(string $type, PlayerTotalSearch $search, array $data): bool
    {
        /** empty search params */
        if(empty($search->tour) || empty($search->surface) || empty($search->add_type)) return false;

        /** additional filters */
        if($search->tour < 0 || $search->surface < 0) return false;

        /** no player added */
        if(count($this->playerTotal) == 0) {
            if($type == PlayerTotal::ACTION['add']) return true;
            else if($type == PlayerTotal::ACTION['remove']) return false;
        }

        /** get player total model */
        $playerTotalModel = PlayerTotal::findOne($data);

        /** get button status */
        switch ($type) {
            case PlayerTotal::ACTION['add']:
                return (is_null($playerTotalModel));
            case PlayerTotal::ACTION['remove']:
                return !(is_null($playerTotalModel));
            default:
                return false;
        }
    }
}
