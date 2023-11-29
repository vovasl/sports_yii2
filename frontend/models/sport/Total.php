<?php

namespace frontend\models\sport;


use frontend\models\sport\query\EventQuery;
use frontend\models\sport\query\TotalQuery;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "sp_total".
 *
 * @property int $id
 * @property int|null $player_id
 * @property int|null $event_id
 * @property int|null $tour_id
 * @property int|null $surface_id
 * @property int $five_sets
 * @property string|null $type
 * @property int|null $min_moneyline
 * @property int|null $profit_0
 * @property int|null $profit_1
 * @property int|null $profit_2
 * @property int|null $profit_3
 * @property int|null $profit_4
 * @property int|null $sum_profit_0
 * @property int|null $sum_profit_1
 * @property int|null $sum_profit_2
 * @property int|null $sum_profit_3
 * @property int|null $sum_profit_4
 *
 * @property Event $event
 * @property Player $player
 * @property Surface $surface
 * @property Tour $tour
 */
class Total extends ActiveRecord
{

    public $count_events;
    public $sum_profit_0;
    public $sum_profit_1;
    public $sum_profit_2;
    public $sum_profit_3;
    public $sum_profit_4;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'sp_total';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['player_id', 'event_id', 'tour_id', 'surface_id', 'five_sets', 'count_events', 'profit_0', 'profit_1', 'profit_2', 'profit_3', 'profit_4', 'sum_profit_0', 'sum_profit_1', 'sum_profit_2', 'sum_profit_3', 'sum_profit_4'], 'integer'],
            [['type', 'min_moneyline'], 'string', 'max' => 255],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => Event::class, 'targetAttribute' => ['event_id' => 'id']],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
            [['surface_id'], 'exist', 'skipOnError' => true, 'targetClass' => Surface::class, 'targetAttribute' => ['surface_id' => 'id']],
            [['tour_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tour::class, 'targetAttribute' => ['tour_id' => 'id']],
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
            'tour_id' => 'Tour ID',
            'surface_id' => 'Surface ID',
            'five_sets' => 'Five Sets',
            'type' => 'Type',
            'min_moneyline' => 'Moneyline',
            'count_events' => 'Events',
            'profit_0' => 'Profit 0',
            'profit_1' => 'Profit 1',
            'profit_2' => 'Profit 2',
            'profit_3' => 'Profit 3',
            'profit_4' => 'Profit 4',
            'sum_profit_0' => '<1.75',
            'sum_profit_1' => '1.75-1.85',
            'sum_profit_2' => '1.85-1.97',
            'sum_profit_3' => '1.97-2.1',
            'sum_profit_4' => '>=2.1',
        ];
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
     * Gets query for [[Player]].
     *
     * @return ActiveQuery
     */
    public function getPlayer(): ActiveQuery
    {
        return $this->hasOne(Player::class, ['id' => 'player_id']);
    }

    /**
     * Gets query for [[Surface]].
     *
     * @return ActiveQuery
     */
    public function getSurface(): ActiveQuery
    {
        return $this->hasOne(Surface::class, ['id' => 'surface_id']);
    }

    /**
     * Gets query for [[Tour]].
     *
     * @return ActiveQuery
     */
    public function getTour(): ActiveQuery
    {
        return $this->hasOne(Tour::class, ['id' => 'tour_id']);
    }
}
